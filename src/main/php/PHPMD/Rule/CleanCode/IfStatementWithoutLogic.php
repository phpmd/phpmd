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

use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTBooleanAndExpression;
use PDepend\Source\AST\ASTBooleanOrExpression;
use PDepend\Source\AST\ASTComment;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTShiftLeftExpression;
use PDepend\Source\AST\ASTShiftRightExpression;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * If Statement Without Logic Rule
 *
 * This rule checks if conditional statements
 * contains any logic. Statements that always
 * resolve with same value trigger violations
 */
class IfStatementWithoutLogic extends AbstractRule implements MethodAware, FunctionAware
{
    private $positives = array(
        'PDepend\Source\AST\ASTLiteral',
        'PDepend\Source\AST\ASTComment',
        'PDepend\Source\AST\ASTExpression',
        'PDepend\Source\AST\ASTBooleanAndExpression',
        'PDepend\Source\AST\ASTBooleanOrExpression',
        'PDepend\Source\AST\ASTAssignmentExpression',
        'PDepend\Source\AST\ASTShiftLeftExpression',
        'PDepend\Source\AST\ASTShiftRightExpression',
    );

    /**
     * This method checks if method/function has if clauses
     * that use assignment instead of comparison.
     *
     * @param AbstractNode $node An instance of MethodNode or FunctionNode class
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $ifStatements = $node->findChildrenOfType('IfStatement');
        $elseIfStatements = $node->findChildrenOfType('ElseIfStatement');

        $statements = array_merge($ifStatements, $elseIfStatements);

        foreach ($statements as $statement) {
            $violating = true;
            foreach ($statement->findChildrenOfType('Expression') as $expression) {
                foreach ($expression->getChildren() as $child) {
                    if (!in_array(get_class($child), $this->positives)) {
                        $violating = false;
                        break 2;
                    }
                }
            }
            if ($violating) {
                $this->addViolation($expression, array($expression->getBeginLine(), $node->getName()));
            }
        }
    }
}
