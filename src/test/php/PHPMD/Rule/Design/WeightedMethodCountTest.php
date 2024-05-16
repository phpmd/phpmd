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

use PHPMD\AbstractTestCase;

/**
 * Test case for the weighted method count rule.
 *
 * @covers \PHPMD\Rule\Design\WeightedMethodCount
 * @since 0.2.5
 */
class WeightedMethodCountTest extends AbstractTestCase
{
    /**
     * testRuleAppliesForValueGreaterThanThreshold
     */
    public function testRuleAppliesForValueGreaterThanThreshold(): void
    {
        $class = $this->getClassMock('wmc', 42);
        $report = $this->getReportWithOneViolation();

        $rule = new WeightedMethodCount();
        $rule->setReport($report);
        $rule->addProperty('maximum', '10');
        $rule->apply($class);
    }

    /**
     * testRuleAppliesForValueEqualToThreshold
     */
    public function testRuleAppliesForValueEqualToThreshold(): void
    {
        $class = $this->getClassMock('wmc', 42);
        $report = $this->getReportWithOneViolation();

        $rule = new WeightedMethodCount();
        $rule->setReport($report);
        $rule->addProperty('maximum', '42');
        $rule->apply($class);
    }

    /**
     * testRuleNotAppliesForValueLowerThanThreshold
     */
    public function testRuleNotAppliesForValueLowerThanThreshold(): void
    {
        $class = $this->getClassMock('wmc', 42);
        $report = $this->getReportWithNoViolation();

        $rule = new WeightedMethodCount();
        $rule->setReport($report);
        $rule->addProperty('maximum', '43');
        $rule->apply($class);
    }
}
