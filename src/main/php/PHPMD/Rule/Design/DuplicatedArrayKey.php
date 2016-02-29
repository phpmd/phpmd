<?php
/**
 * This file is part of PHP Mess Detector.
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
 * @author    Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule\Design;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule detects if array literal has duplicated entries for any key.
 *
 * @author    Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class DuplicatedArrayKey extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * This method checks if a given function or method contains an array literal
     * with duplicated entries for any key and emits a rule violation if so.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('Array') as $array) {
            $this->analyzeArray($array);
        }
    }

    /**
     * Analyzes single array.
     *
     * @param \PHPMD\AbstractNode $node Array node.
     * @return void
     */
    private function analyzeArray(AbstractNode $node)
    {
        // small note regarding implementation - no need for recursion, as `apply()`
        // finds all Array nodes on all depts level

        $keys = [];
        foreach ($node->findChildrenOfType('ArrayElement') as $arrayElement) {
            // member of nested array - will be handled when `apply()` moves to that array
            // could be nice if PDepend provides a method to fetch just direct children
            if ($arrayElement->getParent() != $node) {
                continue;
            }

            $children = $arrayElement->getChildren();
            // non-associative array
            if (count($children) == 1) {
                continue;
            }

            // $children is not wrapped as PHPMD's ASTNodes!
            $arrayKey = $arrayElement->getChild(0);

            // normalize key quoting
            $key = $this->normalizeKey($arrayKey->getName());

            if (isset($keys[$key])) {
                // duplicated key
                $this->addViolation($arrayKey, [$key, $keys[$key]->getBeginLine()]);
            } else {
                // remember first occurance
                $keys[$key] = $arrayKey;
            }
        }
    }

    /**
     * Returns normalized key name.
     *
     * @param string $key Literal key.
     * @return Normalized key.
     */
    private function normalizeKey($key)
    {
        // in PHP keys are considered equal based on value
        // so integer and string with same content will still point to same entry
        // numbers don't need any processing, they have plain value

        // this is string literal, not number
        if (in_array($key[0], ['"', '\''])) {
            $key = stripslashes($key);
            $key = mb_substr($key, 1, -1);
        }

        return $key;
    }
}
