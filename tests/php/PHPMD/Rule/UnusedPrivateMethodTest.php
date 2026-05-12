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
 * Test case for the unused private method rule.
 */
#[CoversClass(UnusedPrivateMethod::class)]
class UnusedPrivateMethodTest extends AbstractTestCase
{
    public function testRuleAppliesToUnusedPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesToUnusedStaticPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesToParentReferencedUnusedPrivateMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesWhenMethodIsReferencedOnDifferentObject(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesWhenMethodIsReferencedOnDifferentClass(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleAppliesWhenPropertyWithSimilarNameIsReferenced(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
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

    public function testRuleDoesNotApplyToMethodUsedViaCallable(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToPrivateConstructor(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToPrivatePhp4Constructor(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToPrivateCloneMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToThisReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToSelfReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToStaticReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToClassNameReferencedMethod(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
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

    public function testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToSelfType(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToStaticType(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToClone(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    public function testRuleDoesNotApplyToNewClassName(): void
    {
        $rule = new UnusedPrivateMethod();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }
}
