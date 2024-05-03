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
 *
 * @link http://phpmd.org/
 */

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;

/**
 * Test case for the {@link \PHPMD\Rule\Design\GotoStatement} class.
 *
 * @covers \PHPMD\Rule\Design\GotoStatement
 *
 * @link https://www.pivotaltracker.com/story/show/10474873
 */
class GotoStatementTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToMethodWithoutGotoStatement
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithoutGotoStatement()
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithGotoStatement
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithGotoStatement()
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutGotoStatement
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithoutGotoStatement()
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithGotoStatement
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithGotoStatement()
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }
}
