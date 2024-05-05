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

use OutOfBoundsException;
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException;
use PDepend\Source\AST\ASTCompoundVariable;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\Node\AbstractCallableNode;
use PHPMD\Node\ASTNode;
use PHPMD\Node\MethodNode;

/**
 * This rule collects all formal parameters of a given function or method that
 * are not used in a statement of the artifact's body.
 *
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class UnusedFormalParameter extends AbstractLocalVariable implements FunctionAware, MethodAware
{
    /**
     * Collected ast nodes.
     *
     * @var array<string, ASTNode<ASTVariableDeclarator>>
     */
    private $nodes = [];

    /**
     * This method checks that all parameters of a given function or method are
     * used at least one time within the artifacts body.
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof AbstractCallableNode) {
            return;
        }

        if ($this->isAbstractMethod($node)) {
            return;
        }

        // Magic methods should be ignored as invalid declarations are picked up by PHP.
        if ($this->isMagicMethod($node)) {
            return;
        }

        if ($this->isInheritedSignature($node)) {
            return;
        }

        if ($this->isNotDeclaration($node)) {
            return;
        }

        $this->nodes = [];

        $this->collectParameters($node);
        $this->removeUsedParameters($node);

        foreach ($this->nodes as $node) {
            $this->addViolation($node, [$node->getImage()]);
        }
    }

    /**
     * Returns <b>true</b> when the given node is an abstract method.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @return bool
     */
    private function isAbstractMethod(AbstractCallableNode $node)
    {
        if ($node instanceof MethodNode) {
            return $node->isAbstract();
        }

        return false;
    }

    /**
     * Returns <b>true</b> when the given node is method with signature declared as inherited using
     * {@inheritDoc} annotation.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @return bool
     */
    private function isInheritedSignature(AbstractCallableNode $node)
    {
        if ($node instanceof MethodNode) {
            $comment = $node->getComment();

            return $comment && preg_match('/@inheritdoc/i', $comment);
        }

        return false;
    }

    /**
     * Returns <b>true</b> when the given node is a magic method signature
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @return bool
     */
    private function isMagicMethod(AbstractCallableNode $node)
    {
        if (!($node instanceof MethodNode)) {
            return false;
        }

        static $magicMethodRegExp = null;

        if ($magicMethodRegExp === null) {
            $magicMethodRegExp = '/__(?:' . implode("|", [
                'call',
                'callStatic',
                'get',
                'set',
                'isset',
                'unset',
                'set_state',
            ]) . ')/i';
        }

        return preg_match($magicMethodRegExp, $node->getName()) === 1;
    }

    /**
     * Tests if the given <b>$node</b> is a method and if this method is also
     * the initial declaration.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @return bool
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @since 1.2.1
     */
    private function isNotDeclaration(AbstractCallableNode $node)
    {
        if ($node instanceof MethodNode) {
            return !$node->isDeclaration();
        }

        return false;
    }

    /**
     * This method extracts all parameters for the given function or method node
     * and it stores the parameter images in the <b>$_images</b> property.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     */
    private function collectParameters(AbstractCallableNode $node): void
    {
        // First collect the formal parameters containers
        foreach ($node->findChildrenOfType(ASTFormalParameters::class) as $parameters) {
            $parent = $parameters->getParentOfType(AbstractASTCallable::class);
            if ($parent->getNode() !== $node->getNode()) {
                continue;
            }

            // Now get all declarators in the formal parameters container
            $declarators = $parameters->findChildrenOfType(ASTVariableDeclarator::class);

            foreach ($declarators as $declarator) {
                $this->nodes[$declarator->getImage()] = $declarator;
            }
        }
    }

    /**
     * This method collects all local variables in the body of the currently
     * analyzed method or function and removes those parameters that are
     * referenced by one of the collected variables.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @throws OutOfBoundsException
     */
    private function removeUsedParameters(AbstractCallableNode $node): void
    {
        $this->removeRegularVariables($node);
        $this->removeCompoundVariables($node);
        $this->removeVariablesUsedByFuncGetArgs($node);
        $this->removePropertyPromotionVariables($node);
    }

    /**
     * Removes all the regular variables from a given node
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node The node to remove the regular variables from.
     * @throws OutOfBoundsException
     */
    private function removeRegularVariables(AbstractCallableNode $node): void
    {
        $variables = $node->findChildrenOfTypeVariable();

        foreach ($variables as $variable) {
            if ($this->isRegularVariable($variable)) {
                unset($this->nodes[$variable->getImage()]);
            }
        }
    }

    /**
     * Removes all the compound variables from a given node
     *
     * Such as
     *
     * <code>
     * // ------
     * Foo::${BAR}();
     * // ------
     *
     * // ------
     * Foo::$${BAR}();
     * // ------
     * </code>
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node The node to remove the compound variables from.
     */
    private function removeCompoundVariables(AbstractCallableNode $node): void
    {
        $compoundVariables = $node->findChildrenOfType(ASTCompoundVariable::class);

        foreach ($compoundVariables as $compoundVariable) {
            $variablePrefix = $compoundVariable->getImage();

            foreach ($compoundVariable->findChildrenOfType(ASTExpression::class) as $child) {
                $variableImage = $variablePrefix . $child->getImage();

                if (isset($this->nodes[$variableImage])) {
                    unset($this->nodes[$variableImage]);
                }
            }
        }
    }

    /**
     * Removes all the variables from a given node, if func_get_args() is called within
     *
     * If the given method calls func_get_args() then all parameters are automatically referenced.
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node The node to remove the referenced variables from.
     */
    private function removeVariablesUsedByFuncGetArgs(AbstractCallableNode $node): void
    {
        $functionCalls = $node->findChildrenOfType(ASTFunctionPostfix::class);

        foreach ($functionCalls as $functionCall) {
            if ($this->isFunctionNameEqual($functionCall, 'func_get_args')) {
                $this->nodes = [];
            }

            if ($this->isFunctionNameEndingWith($functionCall, 'compact')) {
                foreach ($functionCall->findChildrenOfType(ASTLiteral::class) as $literal) {
                    unset($this->nodes['$' . trim($literal->getImage(), '"\'')]);
                }
            }
        }
    }

    /**
     * Removes all the property promotion parameters from a given node
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node The node to remove the property promotion parameters from.
     */
    private function removePropertyPromotionVariables(AbstractCallableNode $node): void
    {
        if (! $node instanceof MethodNode) {
            return;
        }
        if ($node->getImage() !== '__construct') {
            return;
        }

        foreach ($node->findChildrenOfType(ASTFormalParameter::class) as $parameter) {
            if ($parameter->isPromoted()) {
                $variable = $parameter->getFirstChildOfType(ASTVariableDeclarator::class);
                if ($variable !== null) {
                    unset($this->nodes[$variable->getImage()]);
                }
            }
        }
    }
}
