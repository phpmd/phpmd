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

namespace PHPMD\Rule\Naming;

use PHPMD\AbstractTest;

/**
 * Test case for the very short method and function name rule.
 *
 * @covers \PHPMD\Rule\Naming\LongMethodName
 */
class LongMethodNameTest extends AbstractTest
{
    /**
     * @return void
     * @group i
     */
    public function testRuleAppliesToFunctionWithNameLongerThanThreshold()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 10);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithNameEqualToThreshold()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 52);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithNameShorterThanThreshold()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 58);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * @return void
     */
    public function testRuleAppliesToMethodWithNameLongerThanThreshold()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 48);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @return void
     */
    public function testRuleNotAppliesToMethodWithNameEqualToThreshold()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 50);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @return void
     */
    public function testRuleNotAppliesToMethodWithNameShorterThanThreshold()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 58);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @return void
     */
    public function testRuleNotAppliesToMethodWithShortNameWhenException()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 50);
        $rule->addProperty('exceptions', 'testRuleNotAppliesToMethodWithShortNameWhenException,another');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @return void
     */
    public function testRuleAppliesAlsoWithoutExceptionListConfiguredOnMock()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 5);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethodMock());
    }

    /**
     * @return void
     */
    public function testRuleAppliesAlsoWithoutExceptionListConfigured()
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', 5);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }
}
