<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
<<<<<<< HEAD
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@pdepend.org>.
=======
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@phpmd.org>.
>>>>>>> 0.2.x
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
<<<<<<< HEAD
 * @author     Manuel Pichler <mapi@pdepend.org>
=======
 * @author     Manuel Pichler <mapi@phpmd.org>
>>>>>>> 0.2.x
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';
require_once 'PHP/PMD/Rule/IMethodAware.php';

/**
 * This rule collects all local variables within a given function or method
 * that are not used by any code in the analyzed source artifact.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
<<<<<<< HEAD
 * @author     Manuel Pichler <mapi@pdepend.org>
=======
 * @author     Manuel Pichler <mapi@phpmd.org>
>>>>>>> 0.2.x
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Rule_UnusedLocalVariable
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IFunctionAware,
               PHP_PMD_Rule_IMethodAware
{
    /**
     * Found variable images within a single method or function.
     *
     * @var array(string)
     */
    private $_images = array();

    /**
     * This method checks that all local variables within the given function or
     * method are used at least one time.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        $this->_images = array();

        $this->_collectVariables($node);
        $this->_removeParameters($node);
        
        foreach ($this->_images as $image => $nodes) {
            if (count($nodes) === 1) {
                $this->addViolation(reset($nodes), array($image));
            }
        }
    }

    /**
     * This method removes all variables from the <b>$_images</b> property that
     * are also found in the formal parameters of the given method or/and
     * function node.
     *
     * @param PHP_PMD_Node_AbstractMethodOrFunction $node The currently
     *        analyzed method/function node.
     *
     * @return void
     */ 
    private function _removeParameters(PHP_PMD_Node_AbstractMethodOrFunction $node)
    {
        // Get formal parameter container
        $parameters = $node->getFirstChildOfType('FormalParameters');

        // Now get all declarators in the formal parameters container
        $declarators = $parameters->findChildrenOfType('VariableDeclarator');
        
        foreach ($declarators as $declarator) {
            unset($this->_images[$declarator->getImage()]);
        }
    }

    /**
     * This method collects all local variable instances from the given 
     * method/function node and stores their image in the <b>$_images</b>
     * property.
     *
     * @param PHP_PMD_Node_AbstractMethodOrFunction $node The currently
     *        analyzed method/function node.
     *
     * @return void
     */
    private function _collectVariables(PHP_PMD_Node_AbstractMethodOrFunction $node)
    {
        foreach ($node->findChildrenOfType('Variable') as $variable) {
            if ($this->_accept($variable)) {
                $this->_collectVariable($variable);
            }
        }
    }

    /**
     * Stores the given variable node in an internal list of found variables.
     *
     * @param PHP_PMD_Node_ASTNode $node The context variable node.
     *
     * @return void
     */
    private function _collectVariable(PHP_PMD_Node_ASTNode $node)
    {
        if (!isset($this->_images[$node->getImage()])) {
            $this->_images[$node->getImage()] = array();
        }
        $this->_images[$node->getImage()][] = $node;
    }

    /**
     * This method will return <b>true</b> when the given variable node
     * should be tracked during the analyze phase.
     *
     * @param PHP_PMD_AbstractNode $variable The currently analyzed variable node.
     *
     * @return boolean
     */
    private function _accept(PHP_PMD_AbstractNode $variable)
    {
        return $this->_isNotThis($variable) && $this->_isNotStaticPostfix($variable);
    }

    /**
     * This method will return <b>true</b> when the given variable node
     * is not a reference to the objects <b>$this</b> context.
     *
     * @param PHP_PMD_AbstractNode $variable The currently analyzed variable node.
     *
     * @return boolean
     */
    private function _isNotThis(PHP_PMD_AbstractNode $variable)
    {
        return ($variable->getImage() !== '$this');
    }

    /**
     * This method will return <b>true</b> when the given variable node is
     * not the child of a property postfix node.
     *
     * @param PHP_PMD_AbstractNode $variable The currently analyzed variable node.
     *
     * @return boolean
     */
    private function _isNotStaticPostfix(PHP_PMD_AbstractNode $variable)
    {
        $parent = $variable->getParent();
        if (is_object($parent) && $parent->isInstanceOf('PropertyPostfix')) {
            return !($parent->getParent()->getImage() === '::');
        }
        return true;
    }
}
