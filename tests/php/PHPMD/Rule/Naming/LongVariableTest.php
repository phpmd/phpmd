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
 * Test case for the really long variable, parameter and property name rule.
 *
 * @covers \PHPMD\Rule\Naming\LongVariable
 */
class LongVariableTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToLocalVariableInFunctionWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToLocalVariableInFunctionWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '21');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '6');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '6');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionParameterWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToFunctionParameterWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionParameterWithNameSmallerThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToFunctionParameterWithNameSmallerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableInMethodWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToLocalVariableInMethodWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportWithOneViolation());

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '6');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToMethodParameterWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToMethodParameterWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '3');
        $rule->setReport($this->getReportWithOneViolation());

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToMethodParameterWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToMethodParameterWithNameShorterThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToFieldWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameEqualToThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToFieldWithNameEqualToThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '6');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToFieldWithNameShorterThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '8');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldAndParameterWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToFieldAndParameterWithNameLongerThanThreshold(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '3');
        $rule->setReport($this->getReportMock(2));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToStaticMembersAccessedInMethod
     * @throws Throwable
     */
    public function testRuleNotAppliesToStaticMembersAccessedInMethod(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '3');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariableOnlyOneTime
     * @throws Throwable
     */
    public function testRuleAppliesToIdenticalVariableOnlyOneTime(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes
     * @throws Throwable
     */
    public function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
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
     * @throws Throwable
     * @since 1.1.0
     */
    public function testRuleAppliesForLongPrivateProperty(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesForLongPrivateStaticProperty
     *
     * @throws Throwable
     * @since 1.1.0
     */
    public function testRuleAppliesForLongPrivateStaticProperty(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '17');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToVariableNameSmallerThanThresholdWithSuffixSubtracted
     * @throws Throwable
     */
    public function testRuleNotAppliesToVariableNameSmallerThanThresholdWithSuffixSubtracted(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '10');
        $rule->addProperty('subtract-suffixes', 'Repository');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameLongerThanThresholdWithSuffixSubtracted
     * @throws Throwable
     */
    public function testRuleAppliesToVariableNameLongerThanThresholdWithSuffixSubtracted(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '9');
        $rule->addProperty('subtract-suffixes', 'Repository');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameLongerThanThresholdWithMultipleSuffixesDefined
     * @throws Throwable
     */
    public function testRuleAppliesToVariableNameLongerThanThresholdWithMultipleSuffixesDefined(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '19');
        $rule->addProperty('subtract-suffixes', 'Repository,Factory');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameSuffixIsNotSubtractedWhenNotASuffix
     * @throws Throwable
     */
    public function testRuleAppliesToVariableNameSuffixIsNotSubtractedWhenNotASuffix(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '24');
        $rule->addProperty('subtract-suffixes', 'Factory');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameWithEmptySubtractSuffixes
     * @throws Throwable
     */
    public function testRuleAppliesToVariableNameWithEmptySubtractSuffixes(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '20');
        $rule->addProperty('subtract-suffixes', ',');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariableNameFollowingHungarianNotation
     * @throws Throwable
     */
    public function testRuleAppliesToVariableNameFollowingHungarianNotation(): void
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', '12');
        $rule->addProperty('subtract-prefixes', 'arru8');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }
}
