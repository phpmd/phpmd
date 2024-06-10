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
use Throwable;

/**
 * Test case for the unused private field rule.
 *
 * @covers \PHPMD\Rule\UnusedPrivateField
 */
class UnusedPrivateFieldTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToUnusedPrivateField
     * @throws Throwable
     */
    public function testRuleAppliesToUnusedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToUnusedPrivateStaticField
     * @throws Throwable
     */
    public function testRuleAppliesWhenFieldWithSameNameIsAccessedOnDifferentObject(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToUnusedPrivateStaticField
     * @throws Throwable
     */
    public function testRuleAppliesToUnusedPrivateStaticField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnDifferentClass
     * @throws Throwable
     */
    public function testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnDifferentClass(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnParent
     * @throws Throwable
     */
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
     * @throws Throwable
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
     * @throws Throwable
     */
    public function testRuleDoesNotResultInFatalErrorByCallingNonObject(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToUnusedPublicField
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToUnusedPublicField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToUnusedProtectedField
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToUnusedProtectedField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToThisAccessedPrivateField
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToThisAccessedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToSelfAccessedPrivateField
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToSelfAccessedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToStaticAccessedPrivateField
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToStaticAccessedPrivateField(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToClassNameAccessedPrivateField
     * @throws Throwable
     */
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToPrivateStringIndexFieldAccess(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToFieldWithMethodsThatReturnArray
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToFieldWithMethodsThatReturnArray(): void
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }
}
