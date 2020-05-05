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
 * Test case for long class names.
 *
 * @covers PHPMD\Rule\Naming\LongClassName
 */
class LongClassNameTest extends AbstractTest
{
    /**
     * Class name length: 43
     * Threshold: 43
     *
     * @return void
     */
    public function testRuleNotAppliesToClassNameBelowThreshold()
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', 44);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Class name length: 40
     * Threshold: 39
     *
     * @return void
     */
    public function testRuleAppliesToClassNameAboveThreshold()
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', 39);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Interface name length: 47
     * Threshold: 47
     *
     * @return void
     */
    public function testRuleNotAppliesToInterfaceNameBelowThreshold()
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', 47);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getInterface());
    }

    /**
     * Interface name length: 44
     * Threshold: 43
     *
     * @return void
     */
    public function testRuleAppliesToInterfaceNameAboveThreshold()
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', 43);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getInterface());
    }

    /**
     * Class name length: 69
     * Suffix length: 9
     * Threshold: 60
     *
     * @return void
     */
    public function testRuleNotAppliesToClassNameLengthWithSuffixSubtractedBelowThreshold()
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', 60);
        $rule->addProperty('subtract-suffixes', 'Threshold');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Class name length: 66
     * Suffix length: 9
     * Threshold: 56
     *
     * @return void
     */
    public function testRuleAppliesToClassNameLengthWithSuffixSubtractedAboveThreshold()
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', 56);
        $rule->addProperty('subtract-suffixes', 'Threshold');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Class name length: 55
     * Suffix length: 9
     * Threshold: 54
     *
     * @return void
     */
    public function testRuleAppliesToClassNameLengthWithoutSuffixSubtracted()
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', 54);
        $rule->addProperty('subtract-suffixes', 'Threshold');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }
}
