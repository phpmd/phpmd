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

namespace PHPMD\Rule;

use InvalidArgumentException;
use OutOfBoundsException;
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\AbstractASTNode;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTCompoundVariable;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTString;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\Node\AbstractCallableNode;
use PHPMD\Utility\ExceptionsList;

/**
 * This rule collects all local variables within a given function or method
 * that are not used by any code in the analyzed source artifact.
 *
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
final class UnusedLocalVariable extends AbstractLocalVariable implements FunctionAware, MethodAware
{
    /**
     * Found variable images within a single method or function.
     *
     * @var array<string, list<AbstractNode<AbstractASTNode>>>
     */
    private array $images = [];

    /** Temporary cache of configured exceptions. */
    private ExceptionsList $exceptions;

    /**
     * This method checks that all local variables within the given function or
     * method are used at least one time.
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof AbstractCallableNode) {
            return;
        }

        $this->images = [];

        $this->collectVariables($node);
        $this->removeParameters($node);

        foreach ($this->images as $nodes) {
            if (!$this->containsUsages($nodes)) {
                $this->doCheckNodeImage($nodes[0]);
            }
        }
    }

    /**
     * Tests if the given variable node represents a local variable or if it is
     * a static object property or something similar.
     *
     * @param AbstractNode<ASTVariable> $variable The variable to check.
     * @throws OutOfBoundsException
     */
    private function isLocal(AbstractNode $variable): bool
    {
        return (!$variable->isThis()
            && $this->isNotSuperGlobal($variable)
            && $this->isRegularVariable($variable)
        );
    }

    /**
     * Tests if the given variable does not represent one of the PHP super globals
     * that are available in scopes.
     *
     * @param AbstractNode<ASTVariable> $variable
     */
    private function isNotSuperGlobal(AbstractNode $variable): bool
    {
        return !$this->isSuperGlobal($variable);
    }

    /**
     * Return true if one of the passed nodes contains variables usages.
     *
     * @param array<int, AbstractNode<AbstractASTNode>> $nodes
     */
    private function containsUsages(array $nodes): bool
    {
        if (count($nodes) === 1) {
            return false;
        }

        foreach ($nodes as $node) {
            $parent = $node->getParent();

            if (!$parent?->isInstanceOf(ASTAssignmentExpression::class)) {
                return true;
            }

            if (in_array($node->getNode(), array_slice($parent->getChildren(), 1), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This method removes all variables from the <b>$_images</b> property that
     * are also found in the formal parameters of the given method or/and
     * function node.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function removeParameters(AbstractCallableNode $node): void
    {
        // Get formal parameter container
        $parameters = $node->getFirstChildOfType(ASTFormalParameters::class);

        // Now get all declarators in the formal parameters container
        $declarators = $parameters?->findChildrenOfType(ASTVariableDeclarator::class) ?: [];

        foreach ($declarators as $declarator) {
            unset($this->images[$this->getVariableImage($declarator)]);
        }
    }

    /**
     * This method collects all local variable instances from the given
     * method/function node and stores their image in the <b>$_images</b>
     * property.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function collectVariables(AbstractCallableNode $node): void
    {
        foreach ($node->findChildrenOfTypeVariable() as $variable) {
            if ($this->isLocal($variable)) {
                $this->collectVariable($variable);
            }
        }

        foreach ($node->findChildrenOfType(ASTCompoundVariable::class) as $variable) {
            $this->collectCompoundVariableInString($variable);
        }

        foreach ($node->findChildrenOfType(ASTVariableDeclarator::class) as $variable) {
            $parent = $variable->getParentOfType(AbstractASTCallable::class);
            if ($parent?->getNode() === $node->getNode()) {
                $this->collectVariable($variable);
            }
        }

        foreach ($node->findChildrenOfType(ASTFunctionPostfix::class) as $func) {
            if ($this->isFunctionNameEndingWith($func, 'compact')) {
                foreach ($func->findChildrenOfType(ASTLiteral::class) as $literal) {
                    $this->collectLiteral($literal);
                }
            }
        }
    }

    /**
     * Stores the given compound variable node in an internal list of found variables.
     *
     * @param AbstractNode<ASTCompoundVariable> $node
     * @throws OutOfBoundsException
     */
    private function collectCompoundVariableInString(AbstractNode $node): void
    {
        $parentNode = $node->getParent()?->getNode();
        $candidateParentNodes = $node->getParentsOfType(ASTString::class);

        if (in_array($parentNode, $candidateParentNodes, true)) {
            $variablePrefix = $node->getImage();

            foreach ($node->findChildrenOfType(ASTExpression::class) as $child) {
                $variableName = $this->getVariableImage($child);
                $variableImage = $variablePrefix . $variableName;

                $this->storeImage($variableImage, $node);
            }
        }
    }

    /**
     * Stores the given variable node in an internal list of found variables.
     *
     * @param AbstractNode<ASTExpression> $node
     * @throws OutOfBoundsException
     */
    private function collectVariable(AbstractNode $node): void
    {
        $this->storeImage($this->getVariableImage($node->getNode()), $node);
    }

    /**
     * Safely add node to $this->images.
     *
     * @param string $imageName the name to store the node as
     * @param AbstractNode<AbstractASTNode> $node the node being stored
     */
    private function storeImage(string $imageName, AbstractNode $node): void
    {
        if (!isset($this->images[$imageName])) {
            $this->images[$imageName] = [];
        }

        $this->images[$imageName][] = $node;
    }

    /**
     * Stores the given literal node in an internal list of found variables.
     *
     * @param AbstractNode<ASTLiteral> $node
     */
    private function collectLiteral(AbstractNode $node): void
    {
        $variable = '$' . trim($node->getImage(), '\'"');

        if (!isset($this->images[$variable])) {
            $this->images[$variable] = [];
        }

        $this->images[$variable][] = $node;
    }

    /**
     * Template method that performs the real node image check.
     *
     * @param AbstractNode<AbstractASTNode> $node
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function doCheckNodeImage(AbstractNode $node): void
    {
        if ($this->isNameAllowedInContext($node)) {
            return;
        }

        if ($this->isUnusedForeachVariableAllowed($node)) {
            return;
        }

        $image = $this->getVariableImage($node);

        if (str_starts_with($image, '::') || $this->getExceptionsList()->contains(substr($image, 1))) {
            return;
        }

        $parent = $node->getParent();

        // ASTFormalParameter should be handled by the UnusedFormalParameter rule
        if ($parent && $parent->isInstanceOf(ASTFormalParameter::class)) {
            return;
        }

        $this->addViolation($node, [$image]);
    }

    /**
     * Checks if a short name is acceptable in the current context. For the
     * moment these contexts are the init section of a for-loop and short
     * variable names in catch-statements.
     *
     * @param AbstractNode<AbstractASTNode> $node
     */
    private function isNameAllowedInContext(AbstractNode $node): bool
    {
        return $this->isChildOf($node, ASTCatchStatement::class);
    }

    /**
     * Checks if an unused foreach variable (key or variable) is allowed.
     *
     * If it's not a foreach variable, it returns always false.
     *
     * @param AbstractNode<AbstractASTNode> $variable The variable to check.
     * @return bool True if allowed, else false.
     * @throws OutOfBoundsException
     */
    private function isUnusedForeachVariableAllowed(AbstractNode $variable): bool
    {
        $isForeachVariable = $this->isChildOf($variable, ASTForeachStatement::class);

        if (!$isForeachVariable) {
            return false;
        }

        return $this->getBooleanProperty('allow-unused-foreach-variables');
    }

    /**
     * Checks if the given node is a direct or indirect child of a node with
     * the given type.
     *
     * @param AbstractNode<AbstractASTNode> $node
     * @param class-string<AbstractASTNode> $type
     */
    private function isChildOf(AbstractNode $node, $type): bool
    {
        $parent = $node->getParent();

        return $parent && $parent->isInstanceOf($type);
    }

    /**
     * Gets exceptions from property
     */
    private function getExceptionsList(): ExceptionsList
    {
        $this->exceptions ??= new ExceptionsList($this);

        return $this->exceptions;
    }
}
