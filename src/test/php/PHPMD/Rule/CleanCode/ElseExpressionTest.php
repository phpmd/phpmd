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

use PHPMD\AbstractTestCase;
use Throwable;

class ElseExpressionTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToMethodWithoutElseExpression(): void
    {
        $rule = new ElseExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesToMethodWithElseExpression(): void
    {
        $rule = new ElseExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesMultipleTimesToMethodWithMultipleElseExpressions(): void
    {
        $rule = new ElseExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }
}
