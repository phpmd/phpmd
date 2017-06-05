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
 * Test case for the {@link \PHPMD\Rule\Design\DepthOfInheritance} class.
 *
 * @covers \PHPMD\Rule\Design\DepthOfInheritance
 */
class DepthOfInheritanceTest extends AbstractTest
{
    /**
     * testRuleNotAppliesToClassWithNumberOfParentLessThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToClassWithNumberOfParentLessThanThreshold()
    {
        $rule = new DepthOfInheritance();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('dit', 41));
    }

    /**
     * testRuleAppliesToClassWithNumberOfParentIdenticalToThreshold
     *
     * @return void
     */
    public function testRuleAppliesToClassWithNumberOfParentIdenticalToThreshold()
    {
        $rule = new DepthOfInheritance();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('dit', 42));
    }

    /**
     * testRuleAppliesToClassWithNumberOfParentGreaterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToClassWithNumberOfParentGreaterThanThreshold()
    {
        $rule = new DepthOfInheritance();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('dit', 43));
    }
}
