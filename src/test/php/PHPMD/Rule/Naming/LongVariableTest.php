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
 * Test case for the really long variable, parameter and property name rule.
 *_Naming
 *
 * @covers PHPMD\Rule\Naming\LongVariable
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::naming
 * @group unittest
 */
class LongVariableTest extends AbstractTest
{
    /**
     * testRuleAppliesToLocalVariableInFunctionWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToLocalVariableInFunctionWithNameLongerThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionParameterWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFunctionParameterWithNameLongerThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionParameterWithNameSmallerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionParameterWithNameSmallerThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableInMethodWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToLocalVariableInMethodWithNameLongerThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(1));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToMethodParameterWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToMethodParameterWithNameLongerThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 3);
        $rule->setReport($this->getReportMock(1));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToMethodParameterWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodParameterWithNameShorterThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFieldWithNameLongerThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFieldWithNameEqualToThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFieldWithNameShorterThanThreshold()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 8);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldAndParameterWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFieldAndParameterWithNameLongerThanThreshold()
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
     *
     * @return void
     */
    public function testRuleNotAppliesToStaticMembersAccessedInMethod()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 3);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariableOnlyOneTime
     *
     * @return void
     */
    public function testRuleAppliesToIdenticalVariableOnlyOneTime()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes
     *
     * @return void
     */
    public function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes()
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
     * testRuleNotAppliesForLongPrivateProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testRuleNotAppliesForLongPrivateProperty()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesForLongPrivateStaticProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testRuleNotAppliesForLongPrivateStaticProperty()
    {
        $rule = new LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }
}
