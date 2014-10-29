<?php
/**
 * This file is part of PHP Mess Detector.
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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule;

use PHPMD\AbstractTest;

/**
 * Test case for the unused formal parameter rule.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Rule\UnusedFormalParameter
 * @covers \PHPMD\Rule\AbstractLocalVariable
 * @group phpmd
 * @group phpmd::rule
 * @group unittest
 */
class UnusedFormalParameterTest extends AbstractTest
{
    /**
     * testRuleAppliesToFunctionUnusedFormalParameter
     *
     * @return void
     */
    public function testRuleAppliesToFunctionUnusedFormalParameter()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToMultipleFunctionUnusedFormalParameter
     *
     * @return void
     */
    public function testRuleAppliesToMultipleFunctionUnusedFormalParameter()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToMethodUnusedFormalParameter
     *
     * @return void
     */
    public function testRuleAppliesToMethodUnusedFormalParameter()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMultipleMethodUnusedFormalParameter
     *
     * @return void
     */
    public function testRuleAppliesToMultipleMethodUnusedFormalParameter()
    {
        $rule = new UnusedFormalParameter();
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
     */
    public function testRuleAppliesToFormalParameterWhenSimilarStaticMemberIsAccessed()
    {
        $rule = new UnusedFormalParameter();
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
     */
    public function testRuleNotAppliesToFormalParameterUsedInPropertyCompoundVariable()
    {
        $rule = new UnusedFormalParameter();
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
     */
    public function testRuleNotAppliesToFormalParameterUsedInMethodCompoundVariable()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToAbstractMethodFormalParameter
     *
     * @return void
     */
    public function testRuleDoesNotApplyToAbstractMethodFormalParameter()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToInterfaceMethodFormalParameter
     *
     * @return void
     */
    public function testRuleDoesNotApplyToInterfaceMethodFormalParameter()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToInnerFunctionDeclaration
     *
     * @return void
     */
    public function testRuleDoesNotApplyToInnerFunctionDeclaration()
    {
        $rule = new UnusedFormalParameter();
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
     */
    public function testRuleDoesNotApplyToFormalParameterUsedInCompoundExpression()
    {
        $rule = new UnusedFormalParameter();
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
     */
    public function testRuleDoesNotApplyToMethodArgument()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMethodArgumentUsedAsArrayIndex
     *
     * @return void
     */
    public function testRuleDoesNotApplyToMethodArgumentUsedAsArrayIndex()
    {
        $rule = new UnusedFormalParameter();
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
     */
    public function testRuleDoesNotApplyToParameterUsedAsArrayIndex()
    {
        $rule = new UnusedFormalParameter();
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
     *
     * @return void
     */
    public function testRuleDoesNotApplyToParameterUsedAsStringIndex()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMethodWithFuncGetArgs
     *
     * If func_get_args() is called then all parameters are
     * automatically referenced without needing them to be referenced
     * explicitly
     *
     * <code>
     * class Foo {
     *     function bar($baz) {
     *         print_r(func_get_args());
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToMethodWithFuncGetArgs()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.0
     */
    public function test_func_get_args_rule_works_case_insensitive()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToInheritMethod
     *
     * @return void
     * @since 1.2.1
     */
    public function testRuleDoesNotApplyToInheritMethod()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToImplementedAbstractMethod
     *
     * @return void
     * @since 1.2.1
     */
    public function testRuleDoesNotApplyToImplementedAbstractMethod()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToImplementedInterfaceMethod
     *
     * @return void
     * @since 1.2.1
     */
    public function testRuleDoesNotApplyToImplementedInterfaceMethod()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMagicMethod
     *
     * @return void
     */
    public function testRuleDoesNotApplyToMagicMethod()
    {
        $methods = array_filter($this->getClass()->getMethods(), function ($method) {
            return $method->getName() == '__call';
        });

        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply(reset($methods));
    }

    /**
     * testRuleDoesNotApplyToMethodWithInheritdocAnnotation
     */
    public function testRuleDoesNotApplyToMethodWithInheritdocAnnotation()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMethodWithInheritdocAnnotationCamelCase
     */
    public function testRuleDoesNotApplyToMethodWithInheritdocAnnotationCamelCase()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.0
     */
    public function test_compact_function_rule_does_not_apply()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.0
     */
    public function test_compact_function_rule_only_applies_to_used_parameters()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.0
     */
    public function test_compact_function_rule_works_case_insensitive()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.1
     */
    public function test_namespaced_compact_function_rule_does_not_apply()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.1
     */
    public function test_namespaced_compact_function_rule_only_applies_to_used_parameters()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.1
     */
    public function test_namespaced_compact_function_rule_works_case_insensitive()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}
