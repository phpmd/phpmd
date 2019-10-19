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

namespace PHPMD\Rule;

use PHPMD\AbstractTest;

/**
 * Test case for the unused formal parameter rule.
 *
 * @covers \PHPMD\Rule\UnusedFormalParameter
 * @covers \PHPMD\Rule\AbstractLocalVariable
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
    public function testFuncGetArgsRuleWorksCaseInsensitive()
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
    public function testCompactFunctionRuleDoesNotApply()
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
    public function testCompactFunctionRuleOnlyAppliesToUsedParameters()
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
    public function testCompactFunctionRuleWorksCaseInsensitive()
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
    public function testNamespacedCompactFunctionRuleDoesNotApply()
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
    public function testNamespacedCompactFunctionRuleOnlyAppliesToUsedParameters()
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
    public function testNamespacedCompactFunctionRuleWorksCaseInsensitive()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToFormalParameterUsedInStringCompoundVariable
     *
     * <code>
     * class Foo {
     *     public function foo($bar) {
     *         return "me_${bar}";
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToFormalParameterUsedInStringCompoundVariable()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToFormalParameterUsedAsParameterInStringCompoundVariable
     *
     * <code>
     * class Foo {
     *     public function foo($bar) {
     *         return $this->baz("${bar}");
     *     }
     *
     *     private function baz($bar) {
     *         return "who ${bar}?";
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToFormalParameterUsedAsParameterInStringCompoundVariable()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}
