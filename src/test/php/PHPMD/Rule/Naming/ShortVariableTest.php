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
    public function testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToTryCatchBlocksInsideForeach(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '2');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionParameterWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

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

    public function testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '2');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

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

    public function testRuleNotAppliesToMethodParameterWithNameLongerThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '2');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesToFieldWithNameShorterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleNotAppliesToFieldWithNameEqualToThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleNotAppliesToFieldWithNameGreaterThanThreshold(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '2');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

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

    public function testRuleNotAppliesToShortVariableNameAsForLoopIndex(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToShortVariableNameAsForeachLoopIndex(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToShortVariableNameInCatchStatement(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToStaticMembersAccessedInMethod(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToIdenticalVariableOnlyOneTime(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', '');
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

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

    public function testRuleNotAppliesToVariablesFromExceptionsList(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', '3');
        $rule->addProperty('exceptions', 'id');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getClass());
    }

    public function testRuleNotAppliesToVariablesFromExceptionsPattern(): void
    {
        $rule = new ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->addProperty('exceptions', 'foo*');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getClass());
    }

    /**
     * @dataProvider provideClassWithShortForeachVariables
     */
    public function testRuleAppliesToVariablesWithinForeach(bool $allowShortVarInLoop, int $expectedErrorsCount): void
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
