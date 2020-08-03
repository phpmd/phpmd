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
 * Test case for the really short variable, parameter and property name rule.
 *
 * @covers PHPMD\Rule\Naming\ShortVariable
 */
class ShortVariableTest extends AbstractTest
{

    /**
     * testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionParameterWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFunctionParameterWithNameShorterThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableInMethodWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToLocalVariableInMethodWithNameShorterThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());

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
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToMethodParameterWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToMethodParameterWithNameShorterThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToMethodParameterWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodParameterWithNameLongerThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFieldWithNameShorterThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFieldWithNameEqualToThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameGreaterThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFieldWithNameGreaterThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldAndParameterWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFieldAndParameterWithNameShorterThanThreshold()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(2));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToShortVariableNameAsForLoopIndex
     *
     * @return void
     */
    public function testRuleNotAppliesToShortVariableNameAsForLoopIndex()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToShortVariableNameAsForeachLoopIndex
     *
     * @return void
     */
    public function testRuleNotAppliesToShortVariableNameAsForeachLoopIndex()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToShortVariableNameInCatchStatement
     *
     * @return void
     */
    public function testRuleNotAppliesToShortVariableNameInCatchStatement()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToStaticMembersAccessedInMethod
     *
     * @return void
     */
    public function testRuleNotAppliesToStaticMembersAccessedInMethod()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariableOnlyOneTime
     *
     * @return void
     */
    public function testRuleAppliesToIdenticalVariableOnlyOneTime()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
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
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(2));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToVariablesFromExceptionsList
     *
     * @return void
     */
    public function testRuleNotAppliesToVariablesFromExceptionsList()
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', 'id');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariablesWithinForeach
     *
     * @dataProvider provideClassWithShortForeachVariables
     * @return void
     */
    public function testRuleAppliesToVariablesWithinForeach($allowShortVarInLoop, $expectedErrorsCount)
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->addProperty('allow-short-variables-in-loop', $allowShortVarInLoop);
        $rule->setReport($this->getReportMock($expectedErrorsCount));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    public function provideClassWithShortForeachVariables()
    {
        return array(
            array(true, 2),
            array(false, 5),
        );
    }
}
