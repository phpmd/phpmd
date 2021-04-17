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
 * Test case for the excessive long method rule.
 *
 * @covers \PHPMD\Rule\Design\LongMethod
 */
class LongMethodTest extends AbstractTest
{
    /**
     * Tests that the rule applies for a value greater than the configured
     * threshold.
     *
     * @return void
     */
    public function testRuleAppliesForValueGreaterThanThreshold()
    {
        $method = $this->getMethodMock('loc', 42);
        $report = $this->getReportWithOneViolation();

        $rule = new LongMethod();
        $rule->setReport($report);
        $rule->addProperty('minimum', '41');
        $rule->addProperty('ignore-whitespace', false);
        $rule->apply($method);
    }

    /**
     * Test that the rule applies for a value that is equal with the configured
     * threshold.
     *
     * @return void
     */
    public function testRuleAppliesForValueEqualToThreshold()
    {
        $method = $this->getMethodMock('loc', 42);
        $report = $this->getReportWithOneViolation();

        $rule = new LongMethod();
        $rule->setReport($report);
        $rule->addProperty('minimum', '42');
        $rule->addProperty('ignore-whitespace', false);
        $rule->apply($method);
    }

    /**
     * Tests that the rule does not apply when the value is at least one lower
     * than the threshold.
     *
     * @return void
     */
    public function testRuleDoesNotApplyForValueLowerThanThreshold()
    {
        $method = $this->getMethodMock('loc', 22);
        $report = $this->getReportWithNoViolation();

        $rule = new LongMethod();
        $rule->setReport($report);
        $rule->addProperty('minimum', '23');
        $rule->addProperty('ignore-whitespace', false);
        $rule->apply($method);
    }

    /**
     * Tests that the rule uses eloc when ignore whitespace is set
     *
     * @return void
     */
    public function testRuleUsesElocWhenIgnoreWhitespaceSet()
    {
        $class = $this->getClassMock('eloc', 22);
        $report = $this->getReportWithNoViolation();

        $rule = new LongMethod();
        $rule->setReport($report);
        $rule->addProperty('minimum', '23');
        $rule->addProperty('ignore-whitespace', true);
        $rule->apply($class);
    }
}
