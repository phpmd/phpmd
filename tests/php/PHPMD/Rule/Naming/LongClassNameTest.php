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

/**
 * Test cases for LongClassName.
 *
 * @coversDefaultClass  \PHPMD\Rule\Naming\LongClassName
 */
class LongClassNameTest extends AbstractTestCase
{
    /**
     * Tests that the rule does not apply to class name length (43) below threshold (44)
     */
    public function testRuleNotAppliesToClassNameBelowThreshold(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '44');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule applies to class name length (40) below threshold (39)
     */
    public function testRuleAppliesToClassNameAboveThreshold(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '39');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does not apply to interface name length (47) below threshold (47)
     */
    public function testRuleNotAppliesToInterfaceNameBelowThreshold(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '47');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getInterface());
    }

    /**
     * Tests that the rule applies to class name length (44) above threshold (43)
     */
    public function testRuleAppliesToInterfaceNameAboveThreshold(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '43');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getInterface());
    }

    /**
     * Tests that the rule applies to trait name length (40) above threshold (39)
     */
    public function testRuleAppliesToTraitNameAboveThreshold(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '39');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getTrait());
    }

    /**
     * Tests that the rule applies to enum name length (39) above threshold (38)
     */
    public function testRuleAppliesToEnumNameAboveThreshold(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '38');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getEnum());
    }

    /**
     * Tests that the rule does not apply to class name length (69) below threshold (60)
     * with configured suffix length (9)
     */
    public function testRuleNotAppliesToClassNameLengthWithSuffixSubtractedBelowThreshold(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '60');
        $rule->addProperty('subtract-suffixes', 'Threshold');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule applies to class name length (66) above threshold (56) with configured suffix length (9)
     */
    public function testRuleAppliesToClassNameLengthWithSuffixSubtractedAboveThreshold(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '56');
        $rule->addProperty('subtract-suffixes', 'Threshold');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does not apply to class name length (55) below threshold (54)
     * not matching configured suffix length (9)
     */
    public function testRuleAppliesToClassNameLengthWithoutSuffixSubtracted(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '54');
        $rule->addProperty('subtract-suffixes', 'Threshold');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule applies to class name length (43) below threshold (40)
     * not matching configured prefix length (15)
     */
    public function testRuleAppliesToClassNameWithPrefixMatched(): void
    {
        $rule = new LongClassName();
        $rule->addProperty('maximum', '45');
        $rule->addProperty('subtract-prefixes', 'testRule,testRuleApplies');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }
}
