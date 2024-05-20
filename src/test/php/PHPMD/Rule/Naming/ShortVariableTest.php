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
 * Test case for the really short variable, parameter and property name rule.
 *
 * @covers \PHPMD\Rule\Naming\ShortVariable
 */
class ShortVariableTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold
     */
    public function testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToTryCatchBlocks
     */
    public function testRuleNotAppliesToTryCatchBlocksInsideForeach(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionParameterWithNameShorterThanThreshold
     */
    public function testRuleAppliesToFunctionParameterWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold
     */
    public function testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableInMethodWithNameShorterThanThreshold
     */
    public function testRuleAppliesToLocalVariableInMethodWithNameShorterThanThreshold(): void
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
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToMethodParameterWithNameShorterThanThreshold
     */
    public function testRuleAppliesToMethodParameterWithNameShorterThanThreshold(): void
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
     */
    public function testRuleNotAppliesToMethodParameterWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldWithNameShorterThanThreshold
     */
    public function testRuleAppliesToFieldWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameEqualToThreshold
     */
    public function testRuleNotAppliesToFieldWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameGreaterThanThreshold
     */
    public function testRuleNotAppliesToFieldWithNameGreaterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldAndParameterWithNameShorterThanThreshold
     */
    public function testRuleAppliesToFieldAndParameterWithNameShorterThanThreshold(): void
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
     */
    public function testRuleNotAppliesToShortVariableNameAsForLoopIndex(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToShortVariableNameAsForeachLoopIndex
     */
    public function testRuleNotAppliesToShortVariableNameAsForeachLoopIndex(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToShortVariableNameInCatchStatement
     */
    public function testRuleNotAppliesToShortVariableNameInCatchStatement(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToStaticMembersAccessedInMethod
     */
    public function testRuleNotAppliesToStaticMembersAccessedInMethod(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariableOnlyOneTime
     */
    public function testRuleAppliesToIdenticalVariableOnlyOneTime(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes
     */
    public function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes(): void
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
     */
    public function testRuleNotAppliesToVariablesFromExceptionsList(): void
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
     */
    public function testRuleAppliesToVariablesWithinForeach($allowShortVarInLoop, $expectedErrorsCount): void
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

    public static function provideClassWithShortForeachVariables(): array
    {
        return [
            [true, 2],
            [false, 5],
        ];
    }
}
