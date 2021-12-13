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

use PDepend\Source\AST\ASTFormalParameter;
use PHPMD\AbstractNode;
use PHPMD\Node\ASTNode;
use PHPMD\Node\MethodNode;

/**
 * This rule collects all formal parameters of a given function or method that
 * are not used in a statement of the artifact's body.
 */
class UnusedFormalParameter extends AbstractLocalVariable implements FunctionAware, MethodAware
{
    /**
     * Collected ast nodes.
     *
     * @var \PHPMD\Node\ASTNode[]
     */
    protected $nodes = array();

    /**
     * This method checks that all parameters of a given function or method are
     * used at least one time within the artifacts body.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
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

        $this->nodes = array();

        $this->collectParameters($node);
        $this->removeUsedParameters($node);

        foreach ($this->nodes as $node) {
            $this->addViolation($node, array($node->getImage()));
        }
    }

    /**
     * Returns <b>true</b> when the given node is an abstract method.
     *
     * @param \PHPMD\AbstractNode $node
     * @return boolean
     */
    protected function isAbstractMethod(AbstractNode $node)
    {
        if ($node instanceof MethodNode) {
            return $node->isAbstract();
        }

        return false;
    }

    /**
     * Returns <b>true</b> when the given node is method with signature declared as inherited using
     * {@inheritdoc} annotation.
     *
     * @param \PHPMD\AbstractNode $node
     * @return boolean
     */
    protected function isInheritedSignature(AbstractNode $node)
    {
        if ($node instanceof MethodNode) {
            $comment = $node->getDocComment();

            return $comment && preg_match('/@inheritdoc/i', $comment);
        }

        return false;
    }

    /**
     * Returns <b>true</b> when the given node is a magic method signature
     *
     * @param AbstractNode $node
     * @return boolean
     */
    protected function isMagicMethod(AbstractNode $node)
    {
        if (!($node instanceof MethodNode)) {
            return false;
        }

        static $magicMethodRegExp = null;

        if ($magicMethodRegExp === null) {
            $magicMethodRegExp = '/__(?:' . implode("|", array(
                    'call',
                    'callStatic',
                    'get',
                    'set',
                    'isset',
                    'unset',
                    'set_state',
                )) . ')/i';
        }

        return preg_match($magicMethodRegExp, $node->getName()) === 1;
    }

    /**
     * Tests if the given <b>$node</b> is a method and if this method is also
     * the initial declaration.
     *
     * @param \PHPMD\AbstractNode $node
     * @return boolean
     * @since 1.2.1
     */
    protected function isNotDeclaration(AbstractNode $node)
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
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    protected function collectParameters(AbstractNode $node)
    {
        // First collect the formal parameters containers
        foreach ($node->findChildrenOfType('FormalParameters') as $parameters) {
            // Now get all declarators in the formal parameters container
            $declarators = $parameters->findChildrenOfType('VariableDeclarator');

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
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    protected function removeUsedParameters(AbstractNode $node)
    {
        $this->removeRegularVariables($node);
        $this->removeCompoundVariables($node);
        $this->removeVariablesUsedByFuncGetArgs($node);
        $this->removePropertyPromotionVariables($node);
    }

    /**
     * Removes all the regular variables from a given node
     *
     * @param \PHPMD\AbstractNode $node The node to remove the regular variables from.
     * @return void
     */
    protected function removeRegularVariables(AbstractNode $node)
    {
        $variables = $node->findChildrenOfTypeVariable();

        foreach ($variables as $variable) {
            /** @var $variable ASTNode */
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
     * @param \PHPMD\AbstractNode $node The node to remove the compound variables from.
     * @return void
     */
    protected function removeCompoundVariables(AbstractNode $node)
    {
        $compoundVariables = $node->findChildrenOfType('CompoundVariable');

        foreach ($compoundVariables as $compoundVariable) {
            $variablePrefix = $compoundVariable->getImage();

            foreach ($compoundVariable->findChildrenOfType('Expression') as $child) {
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
     * @param \PHPMD\AbstractNode $node The node to remove the referneced variables from.
     * @return void
     */
    protected function removeVariablesUsedByFuncGetArgs(AbstractNode $node)
    {
        $functionCalls = $node->findChildrenOfType('FunctionPostfix');

        foreach ($functionCalls as $functionCall) {
            if ($this->isFunctionNameEqual($functionCall, 'func_get_args')) {
                $this->nodes = array();
            }

            if ($this->isFunctionNameEndingWith($functionCall, 'compact')) {
                foreach ($functionCall->findChildrenOfType('Literal') as $literal) {
                    unset($this->nodes['$' . trim($literal->getImage(), '"\'')]);
                }
            }
        }
    }

    /**
     * Removes all the property promotion parameters from a given node
     *
     * @param \PHPMD\AbstractNode $node The node to remove the property promotion parameters from.
     * @return void
     */
    protected function removePropertyPromotionVariables(AbstractNode $node)
    {
        if (! $node instanceof MethodNode) {
            return;
        }
        if ($node->getImage() !== '__construct') {
            return;
        }

        /** @var ASTFormalParameter&ASTNode $parameter */
        foreach ($node->findChildrenOfType('FormalParameter') as $parameter) {
            if ($parameter->isPromoted()) {
                $variable = $parameter->getFirstChildOfType('VariableDeclarator');
                if ($variable !== null) {
                    unset($this->nodes[$variable->getImage()]);
                }
            }
        }
    }
}
