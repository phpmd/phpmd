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
 * Test case for the unused private field rule.
 *
 * @covers \PHPMD\Rule\UnusedPrivateField
 * @group phpmd
 * @group phpmd::rule
 * @group unittest
 */
class UnusedPrivateFieldTest extends AbstractTest
{
    /**
     * testRuleAppliesToUnusedPrivateField
     *
     * @return void
     */
    public function testRuleAppliesToUnusedPrivateField()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToUnusedPrivateStaticField
     *
     * @return void
     */
    public function testRuleAppliesWhenFieldWithSameNameIsAccessedOnDifferentObject()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToUnusedPrivateStaticField
     *
     * @return void
     */
    public function testRuleAppliesToUnusedPrivateStaticField()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnDifferentClass
     *
     * @return void
     */
    public function testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnDifferentClass()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnParent
     *
     * @return void
     */
    public function testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnParent()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(1));
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
     *
     * @return void
     */
    public function testRuleAppliesWhenLocalVariableIsUsedInStaticMemberPrefix()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(1));
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
     *
     * @return void
     */
    public function testRuleDoesNotResultInFatalErrorByCallingNonObject()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToUnusedPublicField
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUnusedPublicField()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToUnusedProtectedField
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUnusedProtectedField()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToThisAccessedPrivateField
     *
     * @return void
     */
    public function testRuleDoesNotApplyToThisAccessedPrivateField()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToSelfAccessedPrivateField
     *
     * @return void
     */
    public function testRuleDoesNotApplyToSelfAccessedPrivateField()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToStaticAccessedPrivateField
     *
     * @return void
     */
    public function testRuleDoesNotApplyToStaticAccessedPrivateField()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToClassNameAccessedPrivateField
     *
     * @return void
     */
    public function testRuleDoesNotApplyToClassNameAccessedPrivateField()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
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
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivateFieldInChainedMethodCall()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateArrayFieldAccess
     *
     * <code>
     * class Foo {
     *     private $bar = array();
     *     // ...
     *     public function baz() {
     *         return $this->bar[42];
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivateArrayFieldAccess()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
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
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivateStringIndexFieldAccess()
    {
        $rule = new UnusedPrivateField();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }
}
