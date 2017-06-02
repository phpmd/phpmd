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
 * This is a test case for the NPath complexity rule.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Rule\Design\NpathComplexity
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::design
 * @group unittest
 */
class NpathComplexityTest extends AbstractTest
{
    /**
     * Tests that the rule applies for a value greater than the configured
     * threshold.
     *
     * @return void
     */
    public function testRuleAppliesForValueGreaterThanThreshold()
    {
        $method = $this->getMethodMock('npath', 42);
        $report = $this->getReportMock(1);

        $rule = new NpathComplexity();
        $rule->setReport($report);
        $rule->addProperty('minimum', '41');
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
        $method = $this->getMethodMock('npath', 42);
        $report = $this->getReportMock(1);

        $rule = new NpathComplexity();
        $rule->setReport($report);
        $rule->addProperty('minimum', '42');
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
        $method = $this->getMethodMock('npath', 22);
        $report = $this->getReportMock(0);

        $rule = new NpathComplexity();
        $rule->setReport($report);
        $rule->addProperty('minimum', '23');
        $rule->apply($method);
    }
}
