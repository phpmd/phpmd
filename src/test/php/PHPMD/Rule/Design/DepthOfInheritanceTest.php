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
use Throwable;

/**
 * Test case for the {@link \PHPMD\Rule\Design\DepthOfInheritance} class.
 *
 * @covers \PHPMD\Rule\Design\DepthOfInheritance
 */
class DepthOfInheritanceTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToClassWithNumberOfParentLessThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToClassWithNumberOfParentLessThanThreshold(): void
    {
        $rule = new DepthOfInheritance();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('dit', 41));
    }

    /**
     * testRuleAppliesToClassWithNumberOfParentIdenticalToThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToClassWithNumberOfParentIdenticalToThreshold(): void
    {
        $rule = new DepthOfInheritance();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('dit', 42));
    }

    /**
     * testRuleAppliesToClassWithNumberOfParentGreaterThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToClassWithNumberOfParentGreaterThanThreshold(): void
    {
        $rule = new DepthOfInheritance();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('dit', 43));
    }
}
