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

require_once 'PHP/PMD/Rule/Design/TooManyMethods.php';

/**
 * Test case for the too many methods rule.
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
 * @covers PHP_PMD_Rule_Design_TooManyMethods
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::design
 * @group unittest
 */
class PHP_PMD_Rule_Design_TooManyMethodsTest extends PHP_PMD_AbstractTest
{
    /**
     * testRuleDoesNotApplyToClassesWithLessMethodsThanThreshold
     *
     * @return void
     */
    public function testRuleDoesNotApplyToClassesWithLessMethodsThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Design_TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '42');
        $rule->apply($this->_createClassMock(23));
    }

    /**
     * testRuleDoesNotApplyToClassesWithSameNumberOfMethodsAsThreshold
     *
     * @return void
     */
    public function testRuleDoesNotApplyToClassesWithSameNumberOfMethodsAsThreshold()
    {
        $rule = new PHP_PMD_Rule_Design_TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '42');
        $rule->apply($this->_createClassMock(42));
    }

    /**
     * testRuleAppliesToClassesWithMoreMethodsThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToClassesWithMoreMethodsThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Design_TooManyMethods();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('maxmethods', '23');
        $rule->apply($this->_createClassMock(42, array_fill(0, 42, __FUNCTION__)));
    }

    /**
     * testRuleIgnoresGetterMethodsInTest
     *
     * @return void
     */
    public function testRuleIgnoresGetterMethodsInTest()
    {
        $rule = new PHP_PMD_Rule_Design_TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->apply($this->_createClassMock(2, array('invoke', 'getClass')));
    }

    /**
     * testRuleIgnoresSetterMethodsInTest
     *
     * @return void
     */
    public function testRuleIgnoresSetterMethodsInTest()
    {
        $rule = new PHP_PMD_Rule_Design_TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->apply($this->_createClassMock(2, array('invoke', 'setClass')));
    }

   /**
     * testRuleIgnoresCustomMethodsWhenRegexPropertyIsGiven
     *
     * @return void
     */
    public function testRuleIgnoresCustomMethodsWhenRegexPropertyIsGiven()
    {
        $rule = new PHP_PMD_Rule_Design_TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->_createClassMock(2, array('invoke', 'injectClass')));
    }

    /**
     * testRuleIgnoresGetterAndSetterMethodsInTest
     *
     * @return void
     */
    public function testRuleIgnoresGetterAndSetterMethodsInTest()
    {
        $rule = new PHP_PMD_Rule_Design_TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '2');
        $rule->apply($this->_createClassMock(3, array('invoke', 'getClass', 'setClass')));
    }

    /**
     * Creates a prepared class node mock
     *
     * @param integer       $numberOfMethods Number of methods metric value.
     * @param array(string) $methodNames     Name of all methods.
     *
     * @return PHP_PMD_Node_Class
     */
    private function _createClassMock($numberOfMethods, array $methodNames = null)
    {
        $class = $this->getClassMock('nom', $numberOfMethods);

        if (is_array($methodNames)) {
            $class->expects($this->once())
                ->method('getMethodNames')
                ->will($this->returnValue($methodNames));
        }
        return $class;
    }
}
