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

use PHPMD\AbstractTest;

/**
 * Class IfStatementWithoutLogicTest
 * @package PHPMD\Rule\CleanCode
 */
class IfStatementWithoutLogicTest extends AbstractTest
{
    /**
     * All conditions should trigger violation
     *
     * @return void
     */
    public function testRuleAppliesToIfsWithLiteralsOnly()
    {
        $rule = new IfStatementWithoutLogic();
        $rule->setReport($this->getReportMock(14));
        $rule->apply($this->getFunction());
    }

    /**
     * All conditions are valid for this test
     *
     * @return void
     */
    public function testRuleNotAppliesToValidIfs()
    {
        $rule = new IfStatementWithoutLogic();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * All conditions are valid for this test
     *
     * @return void
     */
    public function testRuleNotAppliesToClasses()
    {
        $rule = new IfStatementWithoutLogic();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * 8 conditions should trigger violation
     * 8 conditions are valid
     *
     * @return void
     */
    public function testRuleNotAppliesToElseIfCases()
    {
        $rule = new IfStatementWithoutLogic();
        $rule->setReport($this->getReportMock(8));
        $rule->apply($this->getMethod());
    }

    /**
     * 11 conditions should trigger violation
     * 14 conditions are valid
     *
     * @return void
     */
    public function testRuleNotAppliesToNestedIfs()
    {
        $rule = new IfStatementWithoutLogic();
        $rule->setReport($this->getReportMock(11));
        $rule->apply($this->getMethod());
    }
}
