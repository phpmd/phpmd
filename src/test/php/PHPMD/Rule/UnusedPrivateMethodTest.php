<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule;

use PHPMD\AbstractTest;

/**
 * Test case for the unused private method rule.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
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
}
