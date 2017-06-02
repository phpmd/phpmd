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
 * @covers PHPMD\Rule\Naming\ShortMethodName
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::naming
 * @group unittest
 */
class ShortMethodNameTest extends AbstractTest
{
    /**
     * testRuleAppliesToFunctionWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithNameShorterThanThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 54);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithNameEqualToThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithNameLongerThanThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 54);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }
    /**
     * testRuleAppliesToFunctionWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithNameShorterThanThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithNameEqualToThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 50);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithNameLongerThanThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithShortNameWhenException()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 100);
        $rule->addProperty('exceptions', 'testRuleNotAppliesToMethodWithShortNameWhenException,another');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAlsoWorksWithoutExceptionListConfigured
     *
     * @return void
     * @since 2.2.2
     * @see https://github.com/phpmd/phpmd/issues/80
     * @see https://github.com/phpmd/phpmd/issues/270
     */
    public function testRuleAlsoWorksWithoutExceptionListConfigured()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 100);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethodMock());
    }
}
