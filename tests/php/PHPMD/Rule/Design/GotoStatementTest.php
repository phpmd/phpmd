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
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test case for the {@link \PHPMD\Rule\Design\GotoStatement} class.
 *
 * @link https://www.pivotaltracker.com/story/show/10474873
 */
#[CoversClass(GotoStatement::class)]
class GotoStatementTest extends AbstractTestCase
{
    public function testRuleNotAppliesToMethodWithoutGotoStatement(): void
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithGotoStatement(): void
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToFunctionWithoutGotoStatement(): void
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithGotoStatement(): void
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }
}
