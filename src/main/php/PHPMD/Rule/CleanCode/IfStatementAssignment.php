<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2017, Manuel Pichler <mapi@phpmd.org>.
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
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * @author    Kamil Szymanski <kamilszymanski@gmail.com>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 */
class IfStatementAssignment extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * This method checks if method/function has any if statement that uses assignment instead of comparison.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $statements = $this->getStatements($node);
        $expressions = $this->getExpressions($statements);
        $assignments = $this->getAssignments($expressions);
        $this->addViolations($node, $assignments);
    }

    /**
     * @param AbstractNode $node
     * @return array
     */
    private function getStatements(AbstractNode $node)
    {
        $ifStatements = $node->findChildrenOfType('IfStatement');
        $elseIfStatements = $node->findChildrenOfType('ElseIfStatement');

        return array_merge($ifStatements, $elseIfStatements);
    }

    /**
     * @param array $scopes
     * @return array
     */
    private function getExpressions(array $scopes)
    {
        $expressions = array();
        /** @var ASTNode $scope */
        foreach ($scopes as $scope) {
            $expressions = array_merge($expressions, $scope->findChildrenOfType('Expression'));
        }

        return $expressions;
    }

    /**
     * @param array $expressions
     * @return array
     */
    private function getAssignments(array $expressions)
    {
        $assignments = array();
        /** @var ASTNode $expression */
        foreach ($expressions as $expression) {
            $assignments = array_merge($assignments, $expression->findChildrenOfType('AssignmentExpression'));
        }

        return $assignments;
    }

    /**
     * @param AbstractNode $node
     * @param array $assignments
     */
    private function addViolations(AbstractNode $node, array $assignments)
    {
        $processesViolations = array();
        /** @var \PDepend\Source\AST\AbstractASTNode $assignment */
        foreach ($assignments as $assignment) {
            if (null === $assignment || $assignment->getImage() !== '=') {
                continue;
            }
            $uniqueHash = $assignment->getStartColumn() . ':' . $assignment->getStartLine();
            if (!in_array($uniqueHash, $processesViolations)) {
                $processesViolations[] = $uniqueHash;
                $this->addViolation($node, array($assignment->getStartColumn(), $assignment->getStartLine()));
            }
        }
    }
}
