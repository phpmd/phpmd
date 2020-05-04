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

use PHPMD\AbstractTest;

/**
 * Test case for short class names.
 *
 * @covers PHPMD\Rule\Naming\ShortClassName
 */
class ShortClassNameTest extends AbstractTest
{
    /**
     * Class name length: 43
     * Threshold: 42
     *
     * @return void
     */
    public function testRuleNotAppliesToClassNameAboveThreshold()
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', 42);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Class name length: 40
     * Threshold: 40
     *
     * @return void
     */
    public function testRuleAppliesToClassNameBelowThreshold()
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', 40);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Interface name length: 47
     * Threshold: 46
     *
     * @return void
     */
    public function testRuleNotAppliesToInterfaceNameAboveThreshold()
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', 46);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getInterface());
    }

    /**
     * Interface name length: 44
     * Threshold: 44
     *
     * @return void
     */
    public function testRuleAppliesToInterfaceNameBelowThreshold()
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', 44);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getInterface());
    }

    /**
     * Class name length: 55
     * Threshold: 60
     *
     * @return void
     */
    public function testRuleNotAppliesToClassNameAboveThresholdInExceptions()
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', 60);
        $rule->addProperty('exceptions', 'testRuleNotAppliesToClassNameAboveThresholdInExceptions');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Class name length: 55
     * Threshold: 55
     *
     * @return void
     */
    public function testRuleAppliesToClassNameAboveThresholdNotInExceptions()
    {
        $rule = new ShortClassName();
        $rule->addProperty('minimum', 55);
        $rule->addProperty('exceptions', 'RandomClassName');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }
}
