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

use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTUnaryExpression;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\AST\State;
use PHPMD\AbstractNode;
use PHPMD\Node\AbstractCallableNode;
use PHPMD\Node\ASTNode;
use PHPMD\Node\MethodNode;
use PHPMD\Rule\AbstractLocalVariable;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule collects all undefined variables within a given function or method
 * that are used by any code in the analyzed source artifact.
 */
class UndefinedVariable extends AbstractLocalVariable implements FunctionAware, MethodAware
{
    /**
     * Found variable images within a single method or function.
     *
     * @var array(string)
     */
    protected $images = [];

    /**
     * This method checks that all local variables within the given function or
     * method are used at least one time.
     */
    public function apply(AbstractNode $node): void
    {
        $this->images = [];

        if ($node instanceof MethodNode) {
            $this->collectProperties($node->getNode()->getParent());
        }

        $this->collect($node);

        foreach ($node->findChildrenOfType('PDepend\Source\AST\ASTClass') as $class) {
            /** @var ASTClass $class */

            $this->collectProperties($class);

            foreach ($class->getMethods() as $method) {
                $this->collect(new MethodNode($method));
            }
        }

        foreach ($node->findChildrenOfTypeVariable() as $variable) {
            if ($this->isSuperGlobal($variable) || $this->isPassedByReference($variable)) {
                $this->addVariableDefinition($variable);
            } elseif (!$this->checkVariableDefined($variable, $node)) {
                $this->addViolation($variable, [$this->getVariableImage($variable)]);
            }
        }
    }

    /**
     * Collect variables defined inside a PHPMD entry node (such as MethodNode).
     */
    protected function collect(AbstractNode $node): void
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

    protected function collectProperties($node): void
    {
        if (!($node instanceof ASTClass)) {
            return;
        }

        foreach ($node->getProperties() as $property) {
            if ($property->isStatic()) {
                $this->images['::'.$property->getName()] = $property;
            }
        }
    }

    /**
     * Stores the given literal node in an global of found variables.
     *
     * @param \PHPMD\Node\AbstractNode $node
     */
    protected function collectGlobalStatements(AbstractNode $node): void
    {
        foreach ($node->findChildrenWithParentType('PDepend\Source\AST\ASTGlobalStatement') as $variable) {
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Stores the given literal node in an catch of found variables.
     */
    protected function collectExceptionCatches(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenWithParentType('PDepend\Source\AST\ASTCatchStatement') as $child) {
            if ($child instanceof ASTVariable) {
                $this->addVariableDefinition($child);
            }
        }
    }

    /**
     * Stores the given literal node in an internal list of found variables.
     */
    protected function collectListExpressions(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenWithParentType('PDepend\Source\AST\ASTListExpression') as $variable) {
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Stores the given literal node in an internal foreach of found variables.
     */
    protected function collectForeachStatements(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenWithParentType('PDepend\Source\AST\ASTForeachStatement') as $child) {
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
     */
    protected function collectClosureParameters(AbstractCallableNode $node): void
    {
        $closures = $node->findChildrenOfType('PDepend\Source\AST\ASTClosure');

        foreach ($closures as $closure) {
            $this->collectParameters($closure);
        }
    }

    /**
     * Check if the given variable was defined in the current context before usage.
     *
     * @return bool
     */
    protected function checkVariableDefined(ASTNode $variable, AbstractCallableNode $parentNode)
    {
        $image = $this->getVariableImage($variable);

        return isset($this->images[$image]) || $this->isNameAllowedInContext($parentNode, $variable);
    }

    /**
     * Collect parameter names of method/function.
     *
     * @param \PHPMD\Node\AbstractNode $node
     */
    protected function collectParameters(AbstractNode $node): void
    {
        // Get formal parameter container
        $parameters = $node->getFirstChildOfType('PDepend\Source\AST\ASTFormalParameters');

        // Now get all declarators in the formal parameters container
        $declarators = $parameters->findChildrenOfType('PDepend\Source\AST\ASTVariableDeclarator');

        foreach ($declarators as $declarator) {
            $this->addVariableDefinition($declarator);
        }
    }

    /**
     * Collect assignments of variables.
     */
    protected function collectAssignments(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenOfType('PDepend\Source\AST\ASTAssignmentExpression') as $assignment) {
            $variable = $assignment->getChild(0);

            if ($variable->getNode() instanceof ASTArray) {
                foreach ($variable->findChildrenOfTypeVariable() as $unpackedVariable) {
                    $this->addVariableDefinition($unpackedVariable);
                }

                continue;
            }

            $this->addVariableDefinition($variable);
        }

        foreach ($node->findChildrenOfType('PDepend\Source\AST\ASTStaticVariableDeclaration') as $static) {
            $variable = $static->getChild(0);
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Collect postfix property.
     *
     * @param \PHPMD\Node\AbstractNode $node
     */
    protected function collectPropertyPostfix(AbstractNode $node): void
    {
        foreach ($node->findChildrenWithParentType('PDepend\Source\AST\ASTPropertyPostfix') as $child) {
            if ($child instanceof ASTVariable) {
                $this->addVariableDefinition($child);
            }
        }
    }

    /**
     * Add the variable to images.
     *
     * @param ASTPropertyPostfix|ASTVariable|ASTVariableDeclarator $variable
     */
    protected function addVariableDefinition($variable): void
    {
        $image = $this->getVariableImage($variable);

        if (!isset($this->images[$image])) {
            $this->images[$image] = $variable;
        }
    }

    /**
     * Checks if a short name is acceptable in the current context.
     *
     * @return bool
     */
    protected function isNameAllowedInContext(AbstractCallableNode $node, ASTNode $variable)
    {
        return (
            $node instanceof MethodNode &&
            $variable->getImage() === '$this' &&
            ($node->getModifiers() & State::IS_STATIC) === 0
        );
    }
}
