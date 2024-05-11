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

use PHPMD\AbstractTestCase;

/**
 * Test case for the very short method and function name rule.
 *
 * @covers \PHPMD\Rule\Naming\ShortMethodName
 */
class ShortMethodNameTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToFunctionWithNameShorterThanThreshold
     */
    public function testRuleAppliesToFunctionWithNameShorterThanThreshold(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 54);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNameEqualToThreshold
     */
    public function testRuleNotAppliesToFunctionWithNameEqualToThreshold(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNameLongerThanThreshold
     */
    public function testRuleNotAppliesToFunctionWithNameLongerThanThreshold(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 54);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithNameShorterThanThreshold
     */
    public function testRuleAppliesToMethodWithNameShorterThanThreshold(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNameEqualToThreshold
     */
    public function testRuleNotAppliesToMethodWithNameEqualToThreshold(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 50);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNameLongerThanThreshold
     */
    public function testRuleNotAppliesToMethodWithNameLongerThanThreshold(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNameLongerThanThreshold
     */
    public function testRuleNotAppliesToMethodWithShortNameWhenException(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 100);
        $rule->addProperty('exceptions', 'testRuleNotAppliesToMethodWithShortNameWhenException,another');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAlsoWorksWithoutExceptionListConfigured
     *
     * @link https://github.com/phpmd/phpmd/issues/80
     * @link https://github.com/phpmd/phpmd/issues/270
     * @since 2.2.2
     */
    public function testRuleAppliesAlsoWithoutExceptionListConfiguredOnMock(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 100);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethodMock());
    }

    public function testRuleAppliesAlsoWithoutExceptionListConfigured(): void
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 100);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }
}
