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
 *_Naming
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule\Naming;

use PHPMD\AbstractTest;

/**
 * Test case for the {@link \PHPMD\Rule\Naming\BooleanGetMethodName} rule class.
 *_Naming
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers PHPMD\Rule\Naming\BooleanGetMethodName
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::naming
 * @group unittest
 */
class BooleanGetMethodNameTest extends AbstractTest
{
    /**
     * testRuleAppliesToMethodStartingWithGetAndReturningBoolean
     *
     * @return void
     */
    public function testRuleAppliesToMethodStartingWithGetAndReturningBoolean()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodStartingWithGetAndReturningBool
     *
     * @return void
     */
    public function testRuleAppliesToMethodStartingWithGetAndReturningBool()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToPearPrivateMethodStartingWithGetAndReturningBoolean
     *
     * @return void
     */
    public function testRuleAppliesToPearPrivateMethodStartingWithGetAndReturningBoolean()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleIgnoresParametersWhenNotExplicitConfigured
     *
     * @return void
     */
    public function testRuleIgnoresParametersWhenNotExplicitConfigured()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesWhenParametersAreExplicitEnabled
     *
     * @return void
     */
    public function testRuleNotAppliesWhenParametersAreExplicitEnabled()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'true');
        $rule->setReport($this->getReportMock(0));

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodStartingWithIs
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodStartingWithIs()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportMock(0));

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodStartingWithHas
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodStartingWithHas()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportMock(0));

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithReturnTypeNotBoolean
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithReturnTypeNotBoolean()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportMock(0));

        $rule->apply($this->getMethod());
    }

    /**
     * Returns the first method found in a source file related to the calling
     * test method.
     *
     * @return \PHPMD\Node\MethodNode
     */
    protected function getMethod()
    {
        $methods = $this->getClass()->getMethods();
        return reset($methods);
    }
}
