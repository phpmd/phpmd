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

use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTElseIfStatement;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTIfStatement;
use PDepend\Source\AST\ASTStatement;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\AbstractCallableNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\MethodNode;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * If Statement Assignment Rule
 *
 * This rule covers the following cases:
 * - single assignment in an if clause
 * - multiple assignments in same if clause
 * - assignments in nested if clauses
 * - assignments in elseif clauses
 * - duplicated assignments (multiple conditions before and after *=* sign)
 *
 * Empty if clauses are skipped
 */
final class IfStatementAssignment extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * This method checks if method/function has if clauses
     * that use assignment instead of comparison.
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof AbstractCallableNode) {
            return;
        }

        $statements = $this->getStatements($node);
        $expressions = $this->getExpressions($statements);
        $assignments = $this->getAssignments($expressions);

        $this->addViolations($node, $assignments);
    }

    /**
     * Extracts if and elseif statements from method/function body
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node
     * @return array<int, AbstractNode<ASTStatement>>
     */
    private function getStatements(AbstractCallableNode $node): array
    {
        return [
            ...$node->findChildrenOfType(ASTIfStatement::class),
            ...$node->findChildrenOfType(ASTElseIfStatement::class),
        ];
    }

    /**
     * Extracts all expression from statements array
     *
     * @param array<AbstractNode<ASTStatement>> $statements Array of if and elseif clauses
     * @return list<AbstractNode<ASTExpression>>
     */
    private function getExpressions(array $statements): array
    {
        $nodes = [];

        foreach ($statements as $statement) {
            $node = $statement->getFirstChildOfType(ASTExpression::class);

            if ($node) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    /**
     * Extracts all assignments from expressions array
     *
     * @param array<int, AbstractNode<ASTExpression>> $expressions Array of expressions
     * @return array<int, AbstractNode<ASTAssignmentExpression>>
     */
    private function getAssignments(array $expressions): array
    {
        $assignments = [];
        foreach ($expressions as $expression) {
            $assignments = [
                ...$assignments,
                ...$expression->findChildrenOfType(ASTAssignmentExpression::class),
            ];
        }

        return $assignments;
    }

    /**
     * Signals if any violations have been found in given method or function
     *
     * @param AbstractCallableNode<AbstractASTCallable> $node An instance of MethodNode or FunctionNode class
     * @param array<AbstractNode<ASTAssignmentExpression>> $assignments Array of assignments
     */
    private function addViolations(AbstractCallableNode $node, array $assignments): void
    {
        $processesViolations = [];
        foreach ($assignments as $assignment) {
            if ($assignment->getImage() !== '=') {
                continue;
            }

            $uniqueHash = $assignment->getStartColumn() . ':' . $assignment->getStartLine();
            if (!in_array($uniqueHash, $processesViolations, true)) {
                $processesViolations[] = $uniqueHash;
                $this->addViolation(
                    $node,
                    [(string) $assignment->getStartLine(), (string) $assignment->getStartColumn()]
                );
            }
        }
    }
}
