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
 * This rule collects all local variable usages within a given function or method
 * that are not defined previously by any code in the analyzed source artifact.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @author     Benjamin Eberlei <kontakt@beberlei.de>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Rule_UndefinedVariable
extends PHP_PMD_Rule_AbstractLocalVariable
implements PHP_PMD_Rule_IFunctionAware,
           PHP_PMD_Rule_IMethodAware
{
    /**
     * Found variable assignment images within a single method or function.
     *
     * @var array(string)
     */
    private $images = array();

    /**
     * Computes the usage of undefined variables in the current scope.
     *
     * @param PHP_PMD_AbstractNode $node
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        $this->images = array();

        $this->collectAssignments($node);
        $this->collectParameters($node);

        foreach ($node->findChildrenOfType('Variable') as $variable) {
            if ( ! $this->checkVariableDefined($variable, $node)) {
                $this->addViolation($variable, array($variable->getImage()));
            }
        }
    }

    /**
     * Check if the given variable was defined in the current context before usage.
     *
     * @param PHP_PMD_AbstractNode $variable
     * @param PHP_PMD_AbstractNode $parentNode
     * @return bool
     */
    private function checkVariableDefined(PHP_PMD_AbstractNode $variable, PHP_PMD_AbstractNode $parentNode)
    {
        return isset($this->images[$variable->getImage()]) || $this->isNameAllowedInContext($parentNode);
    }

    /**
     * Collect parameter names of method/function.
     *
     * @param PHP_PMD_Node_AbstractCallable $node
     * @return void
     */
    private function collectParameters(PHP_PMD_Node_AbstractCallable $node)
    {
        // Get formal parameter container
        $parameters = $node->getFirstChildOfType('FormalParameters');

        // Now get all declarators in the formal parameters container
        $declarators = $parameters->findChildrenOfType('VariableDeclarator');

        foreach ($declarators as $declarator) {
            $this->images[$declarator->getImage()] = $declarator;
        }
    }

    /**
     * Collect assignments of variables.
     *
     * @param PHP_PMD_Node_AbstractCallable $node
     * @return void
     */
    private function collectAssignments(PHP_PMD_Node_AbstractCallable $node)
    {
        foreach ($node->findChildrenOfType('AssignmentExpression') as $assignment) {
            $variable = $assignment->getChild(0);

            if ( ! isset($definitions[$variable->getImage()])) {
                $this->images[$variable->getImage()] = $variable;
            }
        }
    }

    /**
     * Checks if a short name is acceptable in the current context.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return boolean
     */
    private function isNameAllowedInContext(PHP_PMD_AbstractNode $node)
    {
        return ($node instanceof PHP_PMD_Node_Method && $variable->getImage() === '$this');
    }
}
