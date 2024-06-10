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
use Throwable;

/**
 * Test cases for ShortClassName rule
 *
 * @coversDefaultClass \PHPMD\Rule\Naming\ShortClassName
 */
class ShortClassNameTest extends AbstractTestCase
{
    /**
     * Tests that rule does not apply to class name length (43) above threshold (43)
     * @throws Throwable
     */
    public function testRuleNotAppliesToClassNameAboveThreshold(): void
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', '43');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Tests that rule applies to class name length (40) below threshold (41)
     * @throws Throwable
     */
    public function testRuleAppliesToClassNameBelowThreshold(): void
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', '41');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Tests that rule does not apply to interface name length (47) above threshold (47)
     * @throws Throwable
     */
    public function testRuleNotAppliesToInterfaceNameAboveThreshold(): void
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', '47');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getInterface());
    }

    /**
     * Tests that rule applies for interface name length (44) below threshold (45)
     * @throws Throwable
     */
    public function testRuleAppliesToInterfaceNameBelowThreshold(): void
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', '45');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getInterface());
    }

    /**
     * Tests that rule does not apply for class name length (55) below threshold (61) when set in exceptions
     * @throws Throwable
     */
    public function testRuleNotAppliesToClassNameBelowThresholdInExceptions(): void
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', '61');
        $rule->addProperty('exceptions', 'testRuleNotAppliesToClassNameBelowThresholdInExceptions');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Tests that rule applies to class name length (55) below threshold (56) when not set in exceptions
     * @throws Throwable
     */
    public function testRuleAppliesToClassNameBelowThresholdNotInExceptions(): void
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', '56');
        $rule->addProperty('exceptions', 'RandomClassName');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }
}
