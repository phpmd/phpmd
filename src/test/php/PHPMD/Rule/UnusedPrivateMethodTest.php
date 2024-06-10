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
 * Test case for the unused private method rule.
 *
 * @covers \PHPMD\Rule\UnusedPrivateMethod
 */
class UnusedPrivateMethodTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToUnusedPrivateMethod
     * @throws Throwable
     */
    public function testRuleAppliesToUnusedPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToUnusedStaticPrivateMethod
     * @throws Throwable
     */
    public function testRuleAppliesToUnusedStaticPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToParentReferencedUnusedPrivateMethod
     * @throws Throwable
     */
    public function testRuleAppliesToParentReferencedUnusedPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenMethodIsReferencedOnDifferentObject
     * @throws Throwable
     */
    public function testRuleAppliesWhenMethodIsReferencedOnDifferentObject(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenMethodIsReferencedOnDifferentClass
     * @throws Throwable
     */
    public function testRuleAppliesWhenMethodIsReferencedOnDifferentClass(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenPropertyWithSimilarNameIsReferenced
     * @throws Throwable
     */
    public function testRuleAppliesWhenPropertyWithSimilarNameIsReferenced(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
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
     * @throws Throwable
     */
    public function testRuleAppliesWhenMethodWithSimilarNameIsInInvocationChain(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToMethodUsedViaCallable
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToMethodUsedViaCallable(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateConstructor
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToPrivateConstructor(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivatePhp4Constructor
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToPrivatePhp4Constructor(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateCloneMethod
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToPrivateCloneMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToThisReferencedMethod
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToThisReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToSelfReferencedMethod
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToSelfReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToStaticReferencedMethod
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToStaticReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToClassNameReferencedMethod
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToClassNameReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
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
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToPrivateMethodInChainedMethodCall(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }
}
