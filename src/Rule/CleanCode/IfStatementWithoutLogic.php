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

use PDepend\Source\AST\ASTElseIfStatement;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTIfStatement;
use PDepend\Source\AST\ASTNode as PDependNode;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * If Statement Without Logic Rule
 *
 * This rule checks if conditional statements
 * contain any logic. Statements that always
 * resolve with same value trigger violations
 */
class IfStatementWithoutLogic extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * List of AST node class names that indicate non-logic expressions.
     *
     * @var list<string>
     */
    private array $positives = [
        'PDepend\Source\AST\ASTLiteral',
        'PDepend\Source\AST\ASTComment',
        'PDepend\Source\AST\ASTExpression',
        'PDepend\Source\AST\ASTBooleanAndExpression',
        'PDepend\Source\AST\ASTBooleanOrExpression',
        'PDepend\Source\AST\ASTAssignmentExpression',
        'PDepend\Source\AST\ASTShiftLeftExpression',
        'PDepend\Source\AST\ASTShiftRightExpression',
    ];

    /**
     * This method checks if a given method/function has if clauses
     * that contain statements without logic.
     *
     * @param AbstractNode<PDependNode> $node An instance of MethodNode or FunctionNode class
     */
    public function apply(AbstractNode $node): void
    {
        $ifStatements = $node->findChildrenOfType(ASTIfStatement::class);
        $elseIfStatements = $node->findChildrenOfType(ASTElseIfStatement::class);

        $statements = array_merge($ifStatements, $elseIfStatements);

        foreach ($statements as $statement) {
            $violating = true;
            $lastExpression = null;
            foreach ($statement->findChildrenOfType(ASTExpression::class) as $expression) {
                $lastExpression = $expression;
                foreach ($expression->getChildren() as $child) {
                    if (!in_array($child::class, $this->positives, true)) {
                        $violating = false;

                        break 2;
                    }
                }
            }
            if ($violating && $lastExpression !== null) {
                $this->addViolation($lastExpression, [(string) $lastExpression->getBeginLine(), $node->getName()]);
            }
        }
    }
}
