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

namespace PHPMD\Rule;

use PHPMD\AbstractTestCase;

/**
 * Test case for the cyclomatic complexity violation rule.
 *
 * @covers \PHPMD\Rule\CyclomaticComplexity
 */
class CyclomaticComplexityTest extends AbstractTestCase
{
    /**
     * Tests that the rule applies for a value greater than the configured
     * threshold.
     *
     * @return void
     */
    public function testRuleAppliesForValueGreaterThanThreshold()
    {
        $method = $this->getMethodMock('ccn2', 42);
        $report = $this->getReportWithOneViolation();

        $rule = new CyclomaticComplexity();
        $rule->setReport($report);
        $rule->addProperty('reportLevel', '10');
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
        $method = $this->getMethodMock('ccn2', 42);
        $report = $this->getReportWithOneViolation();

        $rule = new CyclomaticComplexity();
        $rule->setReport($report);
        $rule->addProperty('reportLevel', '42');
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
        $method = $this->getMethodMock('ccn2', 22);
        $report = $this->getReportWithNoViolation();

        $rule = new CyclomaticComplexity();
        $rule->setReport($report);
        $rule->addProperty('reportLevel', '23');
        $rule->apply($method);
    }
}
