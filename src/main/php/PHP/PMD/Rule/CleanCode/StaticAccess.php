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

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IMethodAware.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';

/**
 * Check if static access is used in a method.
 *
 * Static access is known to cause hard dependencies between classes
 * and is a bad practice.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
 * @author     Benjamin Eberlei <benjamin@qafoo.com>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Rule_CleanCode_StaticAccess
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    /**
     * Method checks for use of static access and warns about it.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        $nodes = $node->findChildrenOfType('MemberPrimaryPrefix');

        foreach ($nodes as $methodCall) {
            if (!$this->isStaticMethodCall($methodCall)) {
                continue;
            }

            $this->addViolation($methodCall, array($methodCall->getImage(), $methodCall->getImage()));
        }
    }

    private function isStaticMethodCall($methodCall)
    {
        return $methodCall->getChild(0)->getNode() instanceof PHP_Depend_Code_ASTClassOrInterfaceReference &&
               $methodCall->getChild(1)->getNode() instanceof PHP_Depend_Code_ASTMethodPostfix &&
               !$this->isCallingParent($methodCall) &&
               !$this->isCallingSelf($methodCall);
    }

    private function isCallingParent($methodCall)
    {
        return $methodCall->getChild(0)->getNode() instanceof PHP_Depend_Code_ASTParentReference;
    }

    private function isCallingSelf($methodCall)
    {
        return $methodCall->getChild(0)->getNode() instanceof PHP_Depend_Code_ASTSelfReference;
    }
}
