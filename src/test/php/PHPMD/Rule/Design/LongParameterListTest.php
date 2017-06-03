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

use PHPMD\AbstractTest;

/**
 * Test case for the excessive long parameter list rule.
 *
 * @covers \PHPMD\Rule\Design\LongParameterList
 */
class LongParameterListTest extends AbstractTest
{
    /**
     * testApplyIgnoresMethodsWithLessParametersThanMinimum
     *
     * @return void
     */
    public function testApplyIgnoresMethodsWithLessParametersThanMinimum()
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('minimum', '4');
        $rule->apply($this->createMethod(3));
    }

    /**
     * testApplyReportsMethodsWithIdenticalParametersAndMinimum
     *
     * @return void
     */
    public function testApplyReportsMethodsWithIdenticalParametersAndMinimum()
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createMethod(3));
    }

    /**
     * testApplyReportsMethodsWithMoreParametersThanMinimum
     *
     * @return void
     */
    public function testApplyReportsMethodsWithMoreParametersThanMinimum()
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createMethod(42));
    }

    /**
     * testApplyIgnoresFunctionsWithLessParametersThanMinimum
     *
     * @return void
     */
    public function testApplyIgnoresFunctionsWithLessParametersThanMinimum()
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('minimum', '4');
        $rule->apply($this->createFunction(3));
    }

    /**
     * testApplyReportsFunctionsWithIdenticalParametersAndMinimum
     *
     * @return void
     */
    public function testApplyReportsFunctionsWithIdenticalParametersAndMinimum()
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createFunction(3));
    }

    /**
     * testApplyReportsFunctionsWithMoreParametersThanMinimum
     *
     * @return void
     */
    public function testApplyReportsFunctionsWithMoreParametersThanMinimum()
    {
        $rule = new LongParameterList();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createFunction(42));
    }

    /**
     * Returns a mocked method instance.
     *
     * @param integer $parameterCount
     * @return \PHPMD\Node\MethodNode
     */
    private function createMethod($parameterCount)
    {
        return $this->initFunctionOrMethodMock($this->getMethodMock(), $parameterCount);
    }

    /**
     * Creates a mocked function node instance.
     *
     * @param integer $parameterCount Number of function parameters.
     *
     * @return \PHPMD\Node\FunctionNode
     */
    private function createFunction($parameterCount)
    {
        return $this->initFunctionOrMethodMock($this->createFunctionMock(), $parameterCount);
    }

    /**
     * Initializes the getParameterCount() method of the given callable.
     *
     * @param \PHPMD\Node\FunctionNode|\PHPMD\Node\MethodNode $mock
     * @param integer $parameterCount
     * @return \PHPMD\Node\FunctionNode|\PHPMD\Node\MethodNode
     */
    private function initFunctionOrMethodMock($mock, $parameterCount)
    {
        $mock->expects($this->once())
            ->method('getParameterCount')
            ->will($this->returnValue($parameterCount));

        return $mock;
    }
}
