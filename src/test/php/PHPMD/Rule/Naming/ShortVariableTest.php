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
 * Test case for the really short variable, parameter and property name rule.
 *
 * @covers \PHPMD\Rule\Naming\ShortVariable
 */
class ShortVariableTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToTryCatchBlocks
     * @throws Throwable
     */
    public function testRuleNotAppliesToTryCatchBlocksInsideForeach(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '2');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionParameterWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToFunctionParameterWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableInMethodWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToLocalVariableInMethodWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
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
     * @throws Throwable
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '2');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToMethodParameterWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToMethodParameterWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
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
     * @throws Throwable
     */
    public function testRuleNotAppliesToMethodParameterWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '2');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToFieldWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameEqualToThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToFieldWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameGreaterThanThreshold
     * @throws Throwable
     */
    public function testRuleNotAppliesToFieldWithNameGreaterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '2');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldAndParameterWithNameShorterThanThreshold
     * @throws Throwable
     */
    public function testRuleAppliesToFieldAndParameterWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
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
     * @throws Throwable
     */
    public function testRuleNotAppliesToShortVariableNameAsForLoopIndex(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToShortVariableNameAsForeachLoopIndex
     * @throws Throwable
     */
    public function testRuleNotAppliesToShortVariableNameAsForeachLoopIndex(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToShortVariableNameInCatchStatement
     * @throws Throwable
     */
    public function testRuleNotAppliesToShortVariableNameInCatchStatement(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToStaticMembersAccessedInMethod
     * @throws Throwable
     */
    public function testRuleNotAppliesToStaticMembersAccessedInMethod(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariableOnlyOneTime
     * @throws Throwable
     */
    public function testRuleAppliesToIdenticalVariableOnlyOneTime(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes
     * @throws Throwable
     */
    public function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
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
     * @throws Throwable
     */
    public function testRuleNotAppliesToVariablesFromExceptionsList(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', 'id');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToVariablesWithinForeach
     *
     * @throws Throwable
     * @dataProvider provideClassWithShortForeachVariables
     */
    public function testRuleAppliesToVariablesWithinForeach(string $allowShortVarInLoop, int $expectedErrorsCount): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->addProperty('allow-short-variables-in-loop', $allowShortVarInLoop);
        $rule->setReport($this->getReportMock($expectedErrorsCount));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * @return list<mixed>
     */
    public static function provideClassWithShortForeachVariables(): array
    {
        return [
            ['1', 2],
            ['0', 5],
        ];
    }
}
