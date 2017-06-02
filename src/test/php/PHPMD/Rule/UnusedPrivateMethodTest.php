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
 * Test case for the unused private method rule.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Rule\UnusedPrivateMethod
 * @group phpmd
 * @group phpmd::rule
 * @group unittest
 */
class UnusedPrivateMethodTest extends AbstractTest
{
    /**
     * testRuleAppliesToUnusedPrivateMethod
     *
     * @return void
     */
    public function testRuleAppliesToUnusedPrivateMethod()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToUnusedStaticPrivateMethod
     *
     * @return void
     */
    public function testRuleAppliesToUnusedStaticPrivateMethod()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToParentReferencedUnusedPrivateMethod
     *
     * @return void
     */
    public function testRuleAppliesToParentReferencedUnusedPrivateMethod()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenMethodIsReferencedOnDifferentObject
     *
     * @return void
     */
    public function testRuleAppliesWhenMethodIsReferencedOnDifferentObject()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenMethodIsReferencedOnDifferentClass
     *
     * @return void
     */
    public function testRuleAppliesWhenMethodIsReferencedOnDifferentClass()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenPropertyWithSimilarNameIsReferenced
     *
     * @return void
     */
    public function testRuleAppliesWhenPropertyWithSimilarNameIsReferenced()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenMethodWithSimilarNameIsInInvocationChain
     *
     * <code>
     * class Foo {
     *     protected $bar;
     *     private function baz();
     *     public function doIt() {
     *         $this->bar->baz();
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleAppliesWhenMethodWithSimilarNameIsInInvocationChain()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateConstructor
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivateConstructor()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivatePhp4Constructor
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivatePhp4Constructor()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateCloneMethod
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivateCloneMethod()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToThisReferencedMethod
     *
     * @return void
     */
    public function testRuleDoesNotApplyToThisReferencedMethod()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToSelfReferencedMethod
     *
     * @return void
     */
    public function testRuleDoesNotApplyToSelfReferencedMethod()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToStaticReferencedMethod
     *
     * @return void
     */
    public function testRuleDoesNotApplyToStaticReferencedMethod()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToClassNameReferencedMethod
     *
     * @return void
     */
    public function testRuleDoesNotApplyToClassNameReferencedMethod()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateMethodInChainedMethodCall
     *
     * <code>
     * class Foo {
     *     private function bar() {
     *         return new \SplObjectStorage();
     *     }
     *     public function add($object) {
     *         $this->bar()->attach($object);
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivateMethodInChainedMethodCall()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo()
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }
}
