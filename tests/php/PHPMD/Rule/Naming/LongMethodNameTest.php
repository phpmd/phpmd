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
use PHPMD\RuleProperty\RulePropertySetter;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test case for the very long method and function name rule.
 */
#[CoversClass(LongMethodName::class)]
class LongMethodNameTest extends AbstractTestCase
{
    public function testRuleAppliesToFunctionWithNameLongerThanThreshold(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '10');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToFunctionWithNameEqualToThreshold(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '52');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToFunctionWithNameShorterThanThreshold(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '58');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToMethodWithNameLongerThanThreshold(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '48');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToMethodWithNameEqualToThreshold(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '50');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToMethodWithNameShorterThanThreshold(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '58');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToMethodWithLongNameWhenException(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '10');
        $rule->addProperty('exceptions', 'testRuleNotAppliesToMethodWithLongNameWhenException,another');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesAlsoWithoutExceptionListConfiguredOnMock(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '5');
        $rule->setReport($this->getReportWithNoViolation());
        RulePropertySetter::setDefaultValues($rule);
        $rule->apply($this->getMethodMock());
    }

    public function testRuleAppliesAlsoWithoutExceptionListConfigured(): void
    {
        $rule = new LongMethodName();
        $rule->addProperty('maximum', '5');
        $rule->setReport($this->getReportWithOneViolation());
        RulePropertySetter::setDefaultValues($rule);
        $rule->apply($this->getMethod());
    }
}
