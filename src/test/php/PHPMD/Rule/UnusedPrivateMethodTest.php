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
 * Test case for the unused private method rule.
 *
 * @covers \PHPMD\Rule\UnusedPrivateMethod
 */
class UnusedPrivateMethodTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToUnusedPrivateMethod
     */
    public function testRuleAppliesToUnusedPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToUnusedStaticPrivateMethod
     */
    public function testRuleAppliesToUnusedStaticPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToParentReferencedUnusedPrivateMethod
     */
    public function testRuleAppliesToParentReferencedUnusedPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenMethodIsReferencedOnDifferentObject
     */
    public function testRuleAppliesWhenMethodIsReferencedOnDifferentObject(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenMethodIsReferencedOnDifferentClass
     */
    public function testRuleAppliesWhenMethodIsReferencedOnDifferentClass(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesWhenPropertyWithSimilarNameIsReferenced
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
     */
    public function testRuleAppliesWhenMethodWithSimilarNameIsInInvocationChain(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToMethodUsedViaCallable
     */
    public function testRuleDoesNotApplyToMethodUsedViaCallable(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateConstructor
     */
    public function testRuleDoesNotApplyToPrivateConstructor(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivatePhp4Constructor
     */
    public function testRuleDoesNotApplyToPrivatePhp4Constructor(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateCloneMethod
     */
    public function testRuleDoesNotApplyToPrivateCloneMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToThisReferencedMethod
     */
    public function testRuleDoesNotApplyToThisReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToSelfReferencedMethod
     */
    public function testRuleDoesNotApplyToSelfReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToStaticReferencedMethod
     */
    public function testRuleDoesNotApplyToStaticReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToClassNameReferencedMethod
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
     */
    public function testRuleDoesNotApplyToPrivateMethodInChainedMethodCall(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo
     */
    public function testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }
}
