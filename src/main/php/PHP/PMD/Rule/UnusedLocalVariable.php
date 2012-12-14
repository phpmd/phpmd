<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
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
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once 'PHP/PMD/Rule/AbstractLocalVariable.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';
require_once 'PHP/PMD/Rule/IMethodAware.php';

/**
 * This rule collects all local variables within a given function or method
 * that are not used by any code in the analyzed source artifact.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Rule_UnusedLocalVariable
       extends PHP_PMD_Rule_AbstractLocalVariable
    implements PHP_PMD_Rule_IFunctionAware,
               PHP_PMD_Rule_IMethodAware
{
    /**
     * Found variable images within a single method or function.
     *
     * @var array(string)
     */
    private $images = array();

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
        $this->images = array();

        $this->collectVariables($node);
        $this->removeParameters($node);

        foreach ($this->images as $nodes) {
            if (count($nodes) === 1) {
                $this->doCheckNodeImage($nodes[0]);
            }
        }
    }

    /**
     * This method removes all variables from the <b>$_images</b> property that
     * are also found in the formal parameters of the given method or/and
     * function node.
     *
     * @param PHP_PMD_Node_AbstractCallable $node The currently
     *        analyzed method/function node.
     *
     * @return void
     */ 
    private function removeParameters(PHP_PMD_Node_AbstractCallable $node)
    {
        // Get formal parameter container
        $parameters = $node->getFirstChildOfType('FormalParameters');

        // Now get all declarators in the formal parameters container
        $declarators = $parameters->findChildrenOfType('VariableDeclarator');

        foreach ($declarators as $declarator) {
            unset($this->images[$declarator->getImage()]);
        }
    }

    /**
     * This method collects all local variable instances from the given 
     * method/function node and stores their image in the <b>$_images</b>
     * property.
     *
     * @param PHP_PMD_Node_AbstractCallable $node The currently
     *        analyzed method/function node.
     *
     * @return void
     */
    private function collectVariables(PHP_PMD_Node_AbstractCallable $node)
    {
        foreach ($node->findChildrenOfType('Variable') as $variable) {
            if ($this->isLocal($variable)) {
                $this->collectVariable($variable);
            }
        }
        foreach ($node->findChildrenOfType('VariableDeclarator') as $variable) {
            $this->collectVariable($variable);
        }
        foreach ($node->findChildrenOfType('FunctionPostfix') as $func) {
            if ($func->getImage() === 'compact') {
                foreach ($func->findChildrenOfType('Literal') as $literal) {
                    $this->_collectLiteral($literal);
                }
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
    private function collectVariable(PHP_PMD_Node_ASTNode $node)
    {
        if (!isset($this->images[$node->getImage()])) {
            $this->images[$node->getImage()] = array();
        }
        $this->images[$node->getImage()][] = $node;
    }

    /**
     * Stores the given literal node in an internal list of found variables.
     *
     * @param PHP_PMD_Node_ASTNode $node The context variable node.
     *
     * @return void
     */
    private function _collectLiteral(PHP_PMD_Node_ASTNode $node)
    {
        $variable = '$' . trim($node->getImage(), '\'');
        if (!isset($this->_images[$variable])) {
            $this->_images[$variable] = array();
        }
        $this->_images[$variable][] = $node;
    }

    /**
     * Template method that performs the real node image check.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    protected function doCheckNodeImage(PHP_PMD_AbstractNode $node)
    {
        if ($this->isNameAllowedInContext($node)) {
            return;
        }
        $this->addViolation($node, array($node->getImage()));
    }

    /**
     * Checks if a short name is acceptable in the current context. For the
     * moment these contexts are the init section of a for-loop and short
     * variable names in catch-statements.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return boolean
     */
    private function isNameAllowedInContext(PHP_PMD_AbstractNode $node)
    {
        return $this->isChildOf($node, 'CatchStatement');
    }

    /**
     * Checks if the given node is a direct or indirect child of a node with
     * the given type.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     * @param string               $type Possible parent type.
     *
     * @return boolean
     */
    private function isChildOf(PHP_PMD_AbstractNode $node, $type)
    {
        $parent = $node->getParent();
        if ($parent->isInstanceOf($type)) {
            return true;
        }
        return false;
    }
}
?>
