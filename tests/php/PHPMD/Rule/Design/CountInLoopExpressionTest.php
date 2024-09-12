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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;
use Throwable;

/**
 * Count In Loop Expression Test
 *
 * @author Kamil Szymanski <kamilszymanski@gmail.com>
 */
class CountInLoopExpressionTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToAllTypesOfLoops
     * @throws Throwable
     */
    public function testRuleAppliesToAllTypesOfLoops(): void
    {
        $rule = new CountInLoopExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotApplyToExpressionElsewhere
     * @throws Throwable
     */
    public function testRuleNotApplyToExpressionElsewhere(): void
    {
        $rule = new CountInLoopExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleApplyToNestedLoops
     * @throws Throwable
     */
    public function testRuleApplyToNestedLoops(): void
    {
        $rule = new CountInLoopExpression();
        $rule->setReport($this->getReportMock(8));
        $rule->apply($this->getFunction());
    }

    /**
     * testMutedRuleAtClassLevel
     * @throws Throwable
     */
    public function testMutedRuleAtClassLevel(): void
    {
        $rule = new CountInLoopExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testMutedRuleAtMethodLevel
     * @throws Throwable
     */
    public function testMutedRuleAtMethodLevel(): void
    {
        $rule = new CountInLoopExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }
}
