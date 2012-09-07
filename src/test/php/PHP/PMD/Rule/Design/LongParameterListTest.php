<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule_Design
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/PMD/Rule/Design/LongParameterList.php';

/**
 * Test case for the excessive long parameter list rule.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule_Design
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 *
 * @covers PHP_PMD_Rule_Design_LongParameterList
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::design
 * @group unittest
 */
class PHP_PMD_Rule_Design_LongParameterListTest extends PHP_PMD_AbstractTest
{
    /**
     * testApplyIgnoresMethodsWithLessParametersThanMinimum
     *
     * @return void
     */
    public function testApplyIgnoresMethodsWithLessParametersThanMinimum()
    {
        $rule = new PHP_PMD_Rule_Design_LongParameterList();
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
        $rule = new PHP_PMD_Rule_Design_LongParameterList();
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
        $rule = new PHP_PMD_Rule_Design_LongParameterList();
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
        $rule = new PHP_PMD_Rule_Design_LongParameterList();
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
        $rule = new PHP_PMD_Rule_Design_LongParameterList();
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
        $rule = new PHP_PMD_Rule_Design_LongParameterList();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '3');
        $rule->apply($this->createFunction(42));
    }

    /**
     * Returns a mocked method instance.
     *
     * @param integer $parameterCount Number of method parameters.
     *
     * @return PHP_PMD_Node_Method
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
     * @return PHP_PMD_Node_Function
     */
    private function createFunction($parameterCount)
    {
        return $this->initFunctionOrMethodMock($this->createFunctionMock(), $parameterCount);
    }

    /**
     * Initializes the getParameterCount() method of the given callable.
     *
     * @param PHP_PMD_Node_Function|PHP_PMD_Node_Method $mock Mocked callable.
     * @param integer $parameterCount Number of parameters.
     *
     * @return PHP_PMD_Node_Function|PHP_PMD_Node_Method
     */
    private function initFunctionOrMethodMock($mock, $parameterCount)
    {
        $mock->expects($this->once())
            ->method('getParameterCount')
            ->will($this->returnValue($parameterCount));

        return $mock;
    }
}
