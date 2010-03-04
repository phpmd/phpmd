<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@phpmd.org>.
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
 * @subpackage Rule_Naming
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/PMD/Rule/Naming/ShortVariable.php';

/**
 * Test case for the really short variable, parameter and property name rule.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule_Naming
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Rule_Naming_ShortVariableTest extends PHP_PMD_AbstractTest
{
    /**
     * testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleAppliesToLocalVariableInFunctionWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionParameterWithNameShorterThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleAppliesToFunctionParameterWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToFunctionParameterWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableInMethodWithNameShorterThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleAppliesToLocalVariableInMethodWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameEqualToThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToMethodParameterWithNameShorterThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleAppliesToMethodParameterWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToMethodParameterWithNameLongerThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToMethodParameterWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldWithNameShorterThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleAppliesToFieldWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameEqualToThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToFieldWithNameEqualToThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameGreaterThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToFieldWithNameGreaterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 2);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldAndParameterWithNameShorterThanThreshold
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleAppliesToFieldAndParameterWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getClass());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToShortVariableNameAsForLoopIndex
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToShortVariableNameAsForLoopIndex()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToShortVariableNameInCatchStatement
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ShortVariable
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToShortVariableNameInCatchStatement()
    {
        $rule = new PHP_PMD_Rule_Naming_ShortVariable();
        $rule->addProperty('minimum', 3);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }
}