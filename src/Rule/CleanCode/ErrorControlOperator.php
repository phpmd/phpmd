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

use PDepend\Source\AST\ASTUnaryExpression;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Error Control Operators Rule
 *
 * This rule detects usage of error control operator (@).
 *
 * @author Kamil Szymanaski <kamil.szymanski@gmail.com>
 * @link http://php.net/manual/en/language.operators.errorcontrol.php
 */
final class ErrorControlOperator extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * Loops trough all class or function nodes and looks for '@' sign.
     */
    public function apply(AbstractNode $node): void
    {
        foreach ($node->findChildrenOfType(ASTUnaryExpression::class) as $unaryExpression) {
            if ($unaryExpression->getImage() === '@') {
                $this->addViolation($node, [(string) $unaryExpression->getBeginLine()]);
            }
        }
    }
}
