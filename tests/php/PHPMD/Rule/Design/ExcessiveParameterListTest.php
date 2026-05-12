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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\MethodNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test case for the excessive long parameter list rule.
 */
#[CoversClass(ExcessiveParameterList::class)]
class ExcessiveParameterListTest extends AbstractTestCase
{
    public function testApplyIgnoresMethodsWithLessParametersThanMinimum(): void
    {
        $rule = new ExcessiveParameterList();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('minimum', '4');
        $rule->apply($this->createMethod(3));
    }

    public function testApplyReportsMethodsWithIdenticalParametersAndMinimum(): void
    {
        $rule = new ExcessiveParameterList();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createMethod(3));
    }

    public function testApplyReportsMethodsWithMoreParametersThanMinimum(): void
    {
        $rule = new ExcessiveParameterList();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createMethod(42));
    }

    public function testApplyIgnoresFunctionsWithLessParametersThanMinimum(): void
    {
        $rule = new ExcessiveParameterList();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('minimum', '4');
        $rule->apply($this->createFunction(3));
    }

    public function testApplyReportsFunctionsWithIdenticalParametersAndMinimum(): void
    {
        $rule = new ExcessiveParameterList();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createFunction(3));
    }

    public function testApplyReportsFunctionsWithMoreParametersThanMinimum(): void
    {
        $rule = new ExcessiveParameterList();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createFunction(42));
    }

    /**
     * Returns a mocked method instance.
     */
    private function createMethod(int $parameterCount): MethodNode
    {
        $method = $this->initFunctionOrMethodMock($this->getMethodMock(), $parameterCount);
        static::assertInstanceOf(MethodNode::class, $method);

        return $method;
    }

    /**
     * Creates a mocked function node instance.
     *
     * @param int $parameterCount Number of function parameters.
     */
    private function createFunction(int $parameterCount): FunctionNode
    {
        $function = $this->initFunctionOrMethodMock($this->createFunctionMock(), $parameterCount);
        static::assertInstanceOf(FunctionNode::class, $function);

        return $function;
    }

    /**
     * Initializes the getParameterCount() method of the given callable.
     *
     * @param (FunctionNode|MethodNode)&MockObject $mock
     */
    private function initFunctionOrMethodMock($mock, int $parameterCount): FunctionNode|MethodNode
    {
        $mock->expects(static::once())
            ->method('getParameterCount')
            ->willReturn($parameterCount);

        return $mock;
    }
}
