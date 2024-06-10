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
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

/**
 * Test case for the excessive long parameter list rule.
 *
 * @covers \PHPMD\Rule\Design\LongParameterList
 */
class LongParameterListTest extends AbstractTestCase
{
    /**
     * testApplyIgnoresMethodsWithLessParametersThanMinimum
     * @throws Throwable
     */
    public function testApplyIgnoresMethodsWithLessParametersThanMinimum(): void
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('minimum', '4');
        $rule->apply($this->createMethod(3));
    }

    /**
     * testApplyReportsMethodsWithIdenticalParametersAndMinimum
     * @throws Throwable
     */
    public function testApplyReportsMethodsWithIdenticalParametersAndMinimum(): void
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createMethod(3));
    }

    /**
     * testApplyReportsMethodsWithMoreParametersThanMinimum
     * @throws Throwable
     */
    public function testApplyReportsMethodsWithMoreParametersThanMinimum(): void
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createMethod(42));
    }

    /**
     * testApplyIgnoresFunctionsWithLessParametersThanMinimum
     * @throws Throwable
     */
    public function testApplyIgnoresFunctionsWithLessParametersThanMinimum(): void
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('minimum', '4');
        $rule->apply($this->createFunction(3));
    }

    /**
     * testApplyReportsFunctionsWithIdenticalParametersAndMinimum
     * @throws Throwable
     */
    public function testApplyReportsFunctionsWithIdenticalParametersAndMinimum(): void
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createFunction(3));
    }

    /**
     * testApplyReportsFunctionsWithMoreParametersThanMinimum
     * @throws Throwable
     */
    public function testApplyReportsFunctionsWithMoreParametersThanMinimum(): void
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createFunction(42));
    }

    /**
     * Returns a mocked method instance.
     *
     * @param int $parameterCount
     * @return MethodNode
     * @throws Throwable
     */
    private function createMethod($parameterCount)
    {
        $method = $this->initFunctionOrMethodMock($this->getMethodMock(), $parameterCount);
        static::assertInstanceOf(MethodNode::class, $method);

        return $method;
    }

    /**
     * Creates a mocked function node instance.
     *
     * @param int $parameterCount Number of function parameters.
     * @return FunctionNode
     * @throws Throwable
     */
    private function createFunction($parameterCount)
    {
        $function = $this->initFunctionOrMethodMock($this->createFunctionMock(), $parameterCount);
        static::assertInstanceOf(FunctionNode::class, $function);

        return $function;
    }

    /**
     * Initializes the getParameterCount() method of the given callable.
     *
     * @param (FunctionNode|MethodNode)&MockObject $mock
     * @param int $parameterCount
     * @return FunctionNode|MethodNode
     * @throws Throwable
     */
    private function initFunctionOrMethodMock($mock, $parameterCount)
    {
        $mock->expects(static::once())
            ->method('getParameterCount')
            ->will(static::returnValue($parameterCount));

        return $mock;
    }
}
