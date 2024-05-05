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
 * Test case for the really long variable, parameter and property name rule.
 *
 * @covers \PHPMD\Rule\Naming\LongVariable
 */
class LongVariableTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToLocalVariableInFunctionWithNameLongerThanThreshold
     */
    public function testRuleAppliesToLocalVariableInFunctionWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 21);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionParameterWithNameLongerThanThreshold
     */
    public function testRuleAppliesToFunctionParameterWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionParameterWithNameSmallerThanThreshold
     */
    public function testRuleNotAppliesToFunctionParameterWithNameSmallerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableInMethodWithNameLongerThanThreshold
     */
    public function testRuleAppliesToLocalVariableInMethodWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportWithOneViolation());

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToMethodParameterWithNameLongerThanThreshold
     */
    public function testRuleAppliesToMethodParameterWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 3);
        $rule->setReport($this->getReportWithOneViolation());

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToMethodParameterWithNameShorterThanThreshold
     */
    public function testRuleNotAppliesToMethodParameterWithNameShorterThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldWithNameLongerThanThreshold
     */
    public function testRuleAppliesToFieldWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameEqualToThreshold
     */
    public function testRuleNotAppliesToFieldWithNameEqualToThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameShorterThanThreshold
     */
    public function testRuleNotAppliesToFieldWithNameShorterThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 8);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldAndParameterWithNameLongerThanThreshold
     */
    public function testRuleAppliesToFieldAndParameterWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 3);
        $rule->setReport($this->getReportMock(2));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToStaticMembersAccessedInMethod
     */
    public function testRuleNotAppliesToStaticMembersAccessedInMethod(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 3);
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariableOnlyOneTime
     */
    public function testRuleAppliesToIdenticalVariableOnlyOneTime(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes
     */
    public function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(2));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleAppliesForLongPrivateProperty
     *
     * @since 1.1.0
     */
    public function testRuleAppliesForLongPrivateProperty(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesForLongPrivateStaticProperty
     *
     * @since 1.1.0
     */
    public function testRuleAppliesForLongPrivateStaticProperty(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToVariableNameSmallerThanThresholdWithSuffixSubtracted
     */
    public function testRuleNotAppliesToVariableNameSmallerThanThresholdWithSuffixSubtracted(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 10);
        $rule->addProperty('subtract-suffixes', 'Repository');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameLongerThanThresholdWithSuffixSubtracted
     */
    public function testRuleAppliesToVariableNameLongerThanThresholdWithSuffixSubtracted(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 9);
        $rule->addProperty('subtract-suffixes', 'Repository');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameLongerThanThresholdWithMultipleSuffixesDefined
     */
    public function testRuleAppliesToVariableNameLongerThanThresholdWithMultipleSuffixesDefined(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 19);
        $rule->addProperty('subtract-suffixes', 'Repository,Factory');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameSuffixIsNotSubtractedWhenNotASuffix
     */
    public function testRuleAppliesToVariableNameSuffixIsNotSubtractedWhenNotASuffix(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 24);
        $rule->addProperty('subtract-suffixes', 'Factory');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameWithEmptySubtractSuffixes
     */
    public function testRuleAppliesToVariableNameWithEmptySubtractSuffixes(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 20);
        $rule->addProperty('subtract-suffixes', ',');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameFollowingHungarianNotation
     */
    public function testRuleAppliesToVariableNameFollowingHungarianNotation(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 12);
        $rule->addProperty('subtract-prefixes', 'arru8');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }
}
