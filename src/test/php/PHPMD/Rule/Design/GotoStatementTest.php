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

use PHPMD\AbstractTest;

/**
 * Test case for the {@link \PHPMD\Rule\Design\GotoStatement} class.
 *
 * @link       https://www.pivotaltracker.com/story/show/10474873
 *
 * @covers \PHPMD\Rule\Design\GotoStatement
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::design
 * @group unittest
 */
class GotoStatementTest extends AbstractTest
{
    /**
     * testRuleNotAppliesToMethodWithoutGotoStatement
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithoutGotoStatement()
    {
        $rule = new GotoStatement();
        $rule->setReport($this->getReportMock(0));
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
        $rule->setReport($this->getReportMock(1));
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
        $rule->setReport($this->getReportMock(0));
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
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }
}
