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
 * Test case for the weighted method count rule.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @since      0.2.5
 *
 * @covers \PHPMD\Rule\Design\WeightedMethodCount
 * @group phpmd
 * @group phpmd::rule
 * @group unittest
 */
class WeightedMethodCountTest extends AbstractTest
{
    /**
     * testRuleAppliesForValueGreaterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesForValueGreaterThanThreshold()
    {
        $class  = $this->getClassMock('wmc', 42);
        $report = $this->getReportMock(1);

        $rule = new WeightedMethodCount();
        $rule->setReport($report);
        $rule->addProperty('maximum', '10');
        $rule->apply($class);
    }

    /**
     * testRuleAppliesForValueEqualToThreshold
     *
     * @return void
     */
    public function testRuleAppliesForValueEqualToThreshold()
    {
        $class  = $this->getClassMock('wmc', 42);
        $report = $this->getReportMock(1);

        $rule = new WeightedMethodCount();
        $rule->setReport($report);
        $rule->addProperty('maximum', '42');
        $rule->apply($class);
    }

    /**
     * testRuleNotAppliesForValueLowerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesForValueLowerThanThreshold()
    {
        $class  = $this->getClassMock('wmc', 42);
        $report = $this->getReportMock(0);

        $rule = new WeightedMethodCount();
        $rule->setReport($report);
        $rule->addProperty('maximum', '43');
        $rule->apply($class);
    }
}
