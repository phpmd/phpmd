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
use PHPMD\Report;
use PHPMD\RuleViolation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test case for the unused private field rule.
 */
#[CoversClass(UnusedPrivateField::class)]
class UnusedPrivateFieldTest extends AbstractTestCase
{
    public function testRuleAppliesToUnusedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesWhenFieldWithSameNameIsAccessedOnDifferentObject(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesToUnusedPrivateStaticField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnDifferentClass(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnParent(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenLocalVariableIsUsedInStaticMemberPrefix
     *
     * <code>
     * class Foo {
     *     private static $_bar = null;
     *
     *     public function baz() {
     *         self::${$_bar = '_bar'} = 42;
     *     }
     * }
     * </code>
     */
    public function testRuleAppliesWhenLocalVariableIsUsedInStaticMemberPrefix(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenLocalVariableIsUsedInStaticMemberPrefix
     *
     * <code>
     * class Foo {
     *     private static $_bar = null;
     *
     *     public function baz() {
     *         self::${'_bar'} = 42;
     *     }
     * }
     * </code>
     */
    public function testRuleDoesNotResultInFatalErrorByCallingNonObject(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToUnusedPublicField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToUnusedProtectedField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToThisAccessedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToSelfAccessedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToStaticAccessedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToClassNameAccessedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateFieldInChainedMethodCall
     *
     * <code>
     * class Foo {
     *     private $bar = null;
     *     // ...
     *     public function baz() {
     *         $this->bar->foobar();
     *     }
     * }
     * </code>
     */
    public function testRuleDoesNotApplyToPrivateFieldInChainedMethodCall(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateArrayFieldAccess
     *
     * <code>
     * class Foo {
     *     private $bar = [];
     *     // ...
     *     public function baz() {
     *         return $this->bar[42];
     *     }
     * }
     * </code>
     */
    public function testRuleDoesNotApplyToPrivateArrayFieldAccess(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateStringIndexFieldAccess
     *
     * <code>
     * class Foo {
     *     private $bar = "Manuel";
     *     // ...
     *     public function baz() {
     *         return $this->bar{3};
     *     }
     * }
     * </code>
     */
    public function testRuleDoesNotApplyToPrivateStringIndexFieldAccess(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToFieldWithMethodsThatReturnArray(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToWhitelistedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->addProperty('exceptions', 'unused');
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesToNonWhitelistedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->addProperty('exceptions', 'someOtherField');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * A field read with the simple interpolation syntax is used: <code>"$this->foo"</code>.
     */
    public function testRuleDoesNotApplyToPrivateFieldReadInSimpleStringInterpolation(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * A field read with the simple interpolation syntax in a heredoc is used.
     */
    public function testRuleDoesNotApplyToPrivateFieldReadInHeredocInterpolation(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * A field read with the nullsafe interpolation syntax is used: <code>"$this?->foo"</code>.
     */
    public function testRuleDoesNotApplyToPrivateFieldReadInNullsafeInterpolation(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Only the property is read by <code>"$this->foo[0]"</code>, the index is plain text.
     */
    public function testRuleDoesNotApplyToPrivateFieldReadInInterpolationWithArrayIndex(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Every field read in one string is used.
     */
    public function testRuleDoesNotApplyToSeveralPrivateFieldsReadInOneString(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * The braced syntax is parsed as a property access and already counts as a read.
     */
    public function testRuleDoesNotApplyToPrivateFieldReadInBracedInterpolation(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Simple interpolation reads one property only: <code>"$this->foo->bar"</code> does not read <code>bar</code>.
     */
    public function testRuleAppliesToPrivateFieldNotReadByFurtherPropertyOfInterpolatedChain(): void
    {
        $reported = [];
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportCollectingFieldNames($reported));
        $rule->apply($this->getClass());

        static::assertSame(['$bar'], $reported);
    }

    /**
     * Single-quoted strings and nowdocs do not interpolate.
     */
    public function testRuleAppliesToPrivateFieldNamedInNowdocAndSingleQuotedString(): void
    {
        $reported = [];
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportCollectingFieldNames($reported));
        $rule->apply($this->getClass());

        static::assertSame(['$foo', '$bar'], $reported);
    }

    /**
     * An escaped dollar sign is plain text: <code>"\$this->foo"</code>.
     */
    public function testRuleAppliesToPrivateFieldNamedInEscapedInterpolation(): void
    {
        $reported = [];
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportCollectingFieldNames($reported));
        $rule->apply($this->getClass());

        static::assertSame(['$foo'], $reported);
    }

    /**
     * <code>"$object->foo"</code> does not read the field of <code>$this</code>.
     */
    public function testRuleAppliesWhenFieldWithSameNameIsInterpolatedOnDifferentObject(): void
    {
        $reported = [];
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportCollectingFieldNames($reported));
        $rule->apply($this->getClass());

        static::assertSame(['$foo'], $reported);
    }

    /**
     * An arrow that is not followed by a property name, or that sits in another string, is not a read.
     */
    public function testRuleAppliesToArrowTextThatIsNotAPropertyRead(): void
    {
        $reported = [];
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportCollectingFieldNames($reported));
        $rule->apply($this->getClass());

        static::assertSame(['$foo', '$bar'], $reported);
    }

    /**
     * <code>"$this[foo]"</code> is an array index on <code>$this</code>, not a property read.
     */
    public function testRuleAppliesToPrivateFieldNamedAsArrayKeyOfThisInString(): void
    {
        $reported = [];
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportCollectingFieldNames($reported));
        $rule->apply($this->getClass());

        static::assertSame(['$foo'], $reported);
    }

    /**
     * Returns a report that stores the field name of every reported violation.
     *
     * @param list<string> $reported Receives the reported field names.
     * @return MockObject&Report
     */
    private function getReportCollectingFieldNames(array &$reported): Report
    {
        $report = $this->getMockBuilder(Report::class)->getMock();
        $report->method('addRuleViolation')
            ->willReturnCallback(static function (RuleViolation $violation) use (&$reported): void {
                $reported = [...$reported, ...($violation->getArgs() ?? [])];
            });

        return $report;
    }
}
