<?php

/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Rule\CleanCode;

use OutOfBoundsException;
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTGlobalStatement;
use PDepend\Source\AST\ASTListExpression;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTStaticVariableDeclaration;
use PDepend\Source\AST\ASTUnaryExpression;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Node\AbstractCallableNode;
use PHPMD\Node\MethodNode;
use PHPMD\Rule\AbstractLocalVariable;
use PHPMD\Rule\Design\CouplingBetweenObjects;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Utility\Seeker;
use PHPMD\Utility\StaticPropertyScope;
use RuntimeException;

/**
 * This rule collects all undefined variables within a given function or method
 * that are used by any code in the analyzed source artifact.
 */
#[SuppressWarnings(CouplingBetweenObjects::class)]
final class UndefinedVariable extends AbstractLocalVariable implements FunctionAware, MethodAware
{
    /**
     * Found variable images within a single method or function.
     *
     * @var array<string, PDependNode>
     */
    private array $images = [];

    /**
     * True when a static property may be declared by a type outside the
     * analyzed sources, so a `self::$name[...]` access cannot be judged.
     */
    private bool $staticsUnknown = false;

    /**
     * This method checks that all local variables within the given function or
     * method are used at least one time.
     *
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof AbstractCallableNode) {
            return;
        }

        $this->images = [];
        $this->staticsUnknown = false;

        if ($node instanceof MethodNode) {
            $parent = $node->getNode()->getParent();
            if ($parent) {
                $this->collectProperties($parent);
            }
        }

        $this->collect($node);

        foreach ($node->findChildrenOfType(ASTClass::class) as $class) {
            $this->collectProperties($class->getNode());

            foreach ($class->getMethods() as $method) {
                $this->collect(new MethodNode($method));
            }
        }

        foreach ($node->findChildrenOfTypeVariable() as $variable) {
            if ($this->isSuperGlobal($variable) || $this->isPassedByReference($variable->getNode())) {
                $this->addVariableDefinition($variable->getNode());
            } elseif (!$this->checkVariableDefined($variable)) {
                $this->addViolation($variable, [$this->getVariableImage($variable)]);
            }
        }
    }

    /**
     * Collect variables defined inside a PHPMD entry node (such as MethodNode).
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collect(AbstractCallableNode $node): void
    {
        $this->collectPropertyPostfix($node);
        $this->collectClosureParameters($node);
        $this->collectForeachStatements($node);
        $this->collectListExpressions($node);
        $this->collectAssignments($node);
        $this->collectParameters($node);
        $this->collectExceptionCatches($node);
        $this->collectGlobalStatements($node);
    }

    /**
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws RuntimeException
     */
    private function collectProperties(AbstractASTClassOrInterface $node): void
    {
        // ASTTrait extends ASTClass.
        if (!($node instanceof ASTClass)) {
            return;
        }

        $scope = new StaticPropertyScope($node);

        if (!$scope->isComplete()) {
            $this->staticsUnknown = true;
        }

        foreach ($scope->getDeclarators() as $image => $declarator) {
            $this->images["::$image"] = $declarator;
        }
    }

    /**
     * Stores the given literal node in an global of found variables.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collectGlobalStatements(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenWithParentType(ASTGlobalStatement::class) as $variable) {
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Stores the given literal node in an catch of found variables.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collectExceptionCatches(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenWithParentType(ASTCatchStatement::class) as $child) {
            if ($child instanceof ASTVariable) {
                $this->addVariableDefinition($child);
            }
        }
    }

    /**
     * Stores the given literal node in an internal list of found variables.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collectListExpressions(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenWithParentType(ASTListExpression::class) as $variable) {
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Stores the given literal node in an internal foreach of found variables.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collectForeachStatements(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenWithParentType(ASTForeachStatement::class) as $child) {
            if ($child instanceof ASTVariable) {
                $this->addVariableDefinition($child);
            }

            if (!($child instanceof ASTUnaryExpression)) {
                continue;
            }

            foreach ($child->getChildren() as $refChildren) {
                if ($refChildren instanceof ASTVariable) {
                    $this->addVariableDefinition($refChildren);
                }
            }
        }
    }

    /**
     * Stores the given literal node in an internal closure of found variables.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collectClosureParameters(AbstractCallableNode $node): void
    {
        $closures = $node->findChildrenOfType(ASTClosure::class);

        foreach ($closures as $closure) {
            $this->collectParameters($closure);
        }
    }

    /**
     * Check if the given variable was defined in the current context before usage.
     *
     * @param AbstractNode<ASTVariable> $variable
     * @throws OutOfBoundsException
     */
    private function checkVariableDefined(AbstractNode $variable): bool
    {
        $image = $this->getVariableImage($variable);

        return isset($this->images[$image])
            || ($this->staticsUnknown && str_starts_with($image, '::'))
            || $this->isNameAllowedInContext($variable);
    }

    /**
     * Collect parameter names of method/function.
     *
     * @param AbstractNode<PDependNode> $node
     * @throws OutOfBoundsException
     */
    private function collectParameters(AbstractNode $node): void
    {
        // Get formal parameter container
        $parameters = $node->getFirstChildOfType(ASTFormalParameters::class);

        // Now get all declarators in the formal parameters container
        $declarators = $parameters?->findChildrenOfType(ASTVariableDeclarator::class) ?? [];

        foreach ($declarators as $declarator) {
            $this->addVariableDefinition($declarator->getNode());
        }
    }

    /**
     * Collect assignments of variables.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collectAssignments(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenOfType(ASTAssignmentExpression::class) as $assignment) {
            $variable = $assignment->getChild(0);

            if ($variable->getNode() instanceof ASTArray) {
                foreach ($variable->findChildrenOfTypeVariable() as $unpackedVariable) {
                    $this->addVariableDefinition($unpackedVariable->getNode());
                }

                continue;
            }

            $this->addVariableDefinition($variable->getNode());
        }

        foreach ($node->findChildrenOfType(ASTStaticVariableDeclaration::class) as $static) {
            $variable = $static->getChild(0)->getNode();
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Collect postfix property.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collectPropertyPostfix(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenWithParentType(ASTPropertyPostfix::class) as $child) {
            if ($child instanceof ASTVariable) {
                $this->addVariableDefinition($child);
            }
        }
    }

    /**
     * Add the variable to images.
     *
     * @throws OutOfBoundsException
     */
    private function addVariableDefinition(PDependNode $variable): void
    {
        $image = $this->getVariableImage($variable);

        if (!isset($this->images[$image])) {
            $this->images[$image] = $variable;
        }
    }

    /**
     * Checks if a short name is acceptable in the current context.
     *
     * @param AbstractNode<ASTVariable> $variable
     */
    private function isNameAllowedInContext(AbstractNode $variable): bool
    {
        return $variable->getImage() === '$this' && Seeker::fromNode($variable)->isInObjectContext();
    }
}
