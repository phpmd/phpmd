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
 * @subpackage Rule
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/PMD/Rule/UnusedFormalParameter.php';

/**
 * Test case for the unused formal parameter rule.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Rule_UnusedFormalParameterTest extends PHP_PMD_AbstractTest
{
    /**
     * testRuleAppliesToFunctionUnusedFormalParameter
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleAppliesToFunctionUnusedFormalParameter()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToMultipleFunctionUnusedFormalParameter
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleAppliesToMultipleFunctionUnusedFormalParameter()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToMethodUnusedFormalParameter
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleAppliesToMethodUnusedFormalParameter()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMultipleMethodUnusedFormalParameter
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleAppliesToMultipleMethodUnusedFormalParameter()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToFormalParameterWhenSimilarStaticMemberIsAccessed
     *
     * <code>
     * class Foo {
     *     public static $bar = null;
     *     public function baz($bar) {
     *         self::$bar = 'fooBar';
     *     }
     * }
     * </code>
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleAppliesToFormalParameterWhenSimilarStaticMemberIsAccessed()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFormalParameterUsedInPropertyCompoundVariable
     *
     * <code>
     * class Foo {
     *     public function baz($bar) {
     *         self::${$bar} = 'fooBar';
     *     }
     * }
     * </code>
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleNotAppliesToFormalParameterUsedInPropertyCompoundVariable()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFormalParameterUsedInMethodCompoundVariable
     *
     * <code>
     * class Foo {
     *     public function baz($bar) {
     *         self::${$bar}();
     *     }
     * }
     * </code>
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleNotAppliesToFormalParameterUsedInMethodCompoundVariable()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToAbstractMethodFormalParameter
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleDoesNotApplyToAbstractMethodFormalParameter()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToInterfaceMethodFormalParameter
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleDoesNotApplyToInterfaceMethodFormalParameter()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToInnerFunctionDeclaration
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleDoesNotApplyToInnerFunctionDeclaration()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleDoesNotApplyToFormalParameterUsedInCompoundExpression
     *
     * <code>
     * class Foo {
     *     public static $bar;
     *     public function baz($bar) {
     *         self::${$bar} = 42;
     *     }
     * }
     * </code>
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleDoesNotApplyToFormalParameterUsedInCompoundExpression()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMethodArgument
     *
     * <code>
     * class Foo {
     *     function bar($baz) {
     *         $this->foo($baz);
     *     }
     * }
     * </code>
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleDoesNotApplyToMethodArgument()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleDoesNotApplyToMethodArgumentUsedAsArrayIndex()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToParameterUsedAsArrayIndex
     *
     * <code>
     * class Foo {
     *     function bar($baz) {
     *         self::$values[$baz];
     *     }
     * }
     * </code>
     *
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleDoesNotApplyToParameterUsedAsArrayIndex()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToParameterUsedAsStringIndex
     *
     * <code>
     * class Foo {
     *     function bar($baz) {
     *         self::$string{$baz};
     *     }
     * }
     * </code>
     * @return void
     * @covers PHP_PMD_Rule_UnusedFormalParameter
     * @covers PHP_PMD_Rule_AbstractLocalVariable
     * @group phpmd
     * @group phpmd::rule
     * @group unittest
     */
    public function testRuleDoesNotApplyToParameterUsedAsStringIndex()
    {
        $rule = new PHP_PMD_Rule_UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}