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

use PHPMD\AbstractTestCase;

/**
 * Test case for the unused formal parameter rule.
 *
 * @covers \PHPMD\Rule\UnusedFormalParameter
 * @covers \PHPMD\Rule\AbstractLocalVariable
 */
class UnusedFormalParameterTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToFunctionUnusedFormalParameter
     */
    public function testRuleAppliesToFunctionUnusedFormalParameter(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToMultipleFunctionUnusedFormalParameter
     */
    public function testRuleAppliesToMultipleFunctionUnusedFormalParameter(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToMethodUnusedFormalParameter
     */
    public function testRuleAppliesToMethodUnusedFormalParameter(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToEnumMethodUnusedFormalParameter
     */
    public function testRuleAppliesToEnumMethodUnusedFormalParameter(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToClosureUnusedFormalParameter
     */
    public function testRuleAppliesToClosureUnusedFormalParameter(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMultipleMethodUnusedFormalParameter
     */
    public function testRuleAppliesToMultipleMethodUnusedFormalParameter(): void
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
     */
    public function testRuleAppliesToFormalParameterWhenSimilarStaticMemberIsAccessed(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithOneViolation());
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
     */
    public function testRuleNotAppliesToFormalParameterUsedInPropertyCompoundVariable(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
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
     */
    public function testRuleNotAppliesToFormalParameterUsedInMethodCompoundVariable(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToAbstractMethodFormalParameter
     */
    public function testRuleDoesNotApplyToAbstractMethodFormalParameter(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToInterfaceMethodFormalParameter
     */
    public function testRuleDoesNotApplyToInterfaceMethodFormalParameter(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToInnerFunctionDeclaration
     */
    public function testRuleDoesNotApplyToInnerFunctionDeclaration(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
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
     */
    public function testRuleDoesNotApplyToFormalParameterUsedInCompoundExpression(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
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
     */
    public function testRuleDoesNotApplyToMethodArgument(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMethodArgumentUsedAsArrayIndex
     */
    public function testRuleDoesNotApplyToMethodArgumentUsedAsArrayIndex(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
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
     */
    public function testRuleDoesNotApplyToParameterUsedAsArrayIndex(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
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
     */
    public function testRuleDoesNotApplyToParameterUsedAsStringIndex(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
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
     */
    public function testRuleDoesNotApplyToMethodWithFuncGetArgs(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @since 2.0.0
     */
    public function testFuncGetArgsRuleWorksCaseInsensitive(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToInheritMethod
     *
     * @since 1.2.1
     */
    public function testRuleDoesNotApplyToInheritMethod(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToImplementedAbstractMethod
     *
     * @since 1.2.1
     */
    public function testRuleDoesNotApplyToImplementedAbstractMethod(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToImplementedInterfaceMethod
     *
     * @since 1.2.1
     */
    public function testRuleDoesNotApplyToImplementedInterfaceMethod(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMagicMethod
     */
    public function testRuleDoesNotApplyToMagicMethod(): void
    {
        $methods = array_filter(
            $this->getClass()->getMethods(),
            static fn($method) => $method->getName() === '__call',
        );

        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply(reset($methods));
    }

    /**
     * testRuleDoesNotApplyToMethodWithInheritdocAnnotation
     */
    public function testRuleDoesNotApplyToMethodWithInheritdocAnnotation(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMethodWithInheritdocAnnotationCamelCase
     */
    public function testRuleDoesNotApplyToMethodWithInheritdocAnnotationCamelCase(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @since 2.0.0
     */
    public function testCompactFunctionRuleDoesNotApply(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @since 2.0.0
     */
    public function testCompactFunctionRuleOnlyAppliesToUsedParameters(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @since 2.0.0
     */
    public function testCompactFunctionRuleWorksCaseInsensitive(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @since 2.0.1
     */
    public function testNamespacedCompactFunctionRuleDoesNotApply(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @since 2.0.1
     */
    public function testNamespacedCompactFunctionRuleOnlyAppliesToUsedParameters(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @since 2.0.1
     */
    public function testNamespacedCompactFunctionRuleWorksCaseInsensitive(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
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
     */
    public function testRuleDoesNotApplyToFormalParameterUsedInStringCompoundVariable(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
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
     */
    public function testRuleDoesNotApplyToFormalParameterUsedAsParameterInStringCompoundVariable(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToPropertyPromotionParameters
     *
     * <code>
     * class Foo {
     *     public function __construct(private string $foo) {}
     * }
     * </code>
     */
    public function testRuleDoesNotApplyToPropertyPromotionParameters(): void
    {
        $methods = array_filter(
            $this->getClass()->getMethods(),
            static fn($method) => $method->getImage() === '__construct',
        );

        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($methods[0]);
    }
}
