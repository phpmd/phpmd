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
 * @subpackage Rule_Naming
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/PMD/Rule/Naming/LongVariable.php';

/**
 * Test case for the really long variable, parameter and property name rule.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule_Naming
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 *
 * @covers PHP_PMD_Rule_Naming_LongVariable
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::naming
 * @group unittest
 */
class PHP_PMD_Rule_Naming_LongVariableTest extends PHP_PMD_AbstractTest
{
    /**
     * testRuleAppliesToLocalVariableInFunctionWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToLocalVariableInFunctionWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionParameterWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFunctionParameterWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionParameterWithNameSmallerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionParameterWithNameSmallerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableInMethodWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToLocalVariableInMethodWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(1));

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
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToMethodParameterWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToMethodParameterWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 3);
        $rule->setReport($this->getReportMock(1));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToMethodParameterWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodParameterWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFieldWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFieldWithNameEqualToThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 6);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToFieldWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFieldWithNameShorterThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 8);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToFieldAndParameterWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFieldAndParameterWithNameLongerThanThreshold()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 3);
        $rule->setReport($this->getReportMock(2));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }

    /**
     * testRuleNotAppliesToStaticMembersAccessedInMethod
     *
     * @return void
     */
    public function testRuleNotAppliesToStaticMembersAccessedInMethod()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 3);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToIdenticalVariableOnlyOneTime
     *
     * @return void
     */
    public function testRuleAppliesToIdenticalVariableOnlyOneTime()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
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
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(2));

        $class = $this->getClass();
        $rule->apply($class);

        foreach ($class->getMethods() as $method) {
            $rule->apply($method);
        }
    }
    /**
     * testRuleNotAppliesForLongPrivateProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testRuleNotAppliesForLongPrivateProperty()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesForLongPrivateStaticProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testRuleNotAppliesForLongPrivateStaticProperty()
    {
        $rule = new PHP_PMD_Rule_Naming_LongVariable();
        $rule->addProperty('maximum', 17);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }
}
