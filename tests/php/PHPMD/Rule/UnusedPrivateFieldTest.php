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
use PHPUnit\Framework\Attributes\CoversClass;

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
}
