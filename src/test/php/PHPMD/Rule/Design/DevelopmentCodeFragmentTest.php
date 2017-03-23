<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2017, Manuel Pichler <mapi@phpmd.org>.
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
 *_Design
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTest;

/**
 * Test case for the {@link \PHPMD\Rule\Design\DevelopmentCodeFragment} class.
 *_Design
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @see https://github.com/phpmd/phpmd/issues/265
 * @since 2.3.0
 *
 * @covers \PHPMD\Rule\Design\DevelopmentCodeFragment
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::design
 * @group unittest
 */
class DevelopmentCodeFragmentTest extends AbstractTest
{
    /**
     * testRuleNotAppliesToMethodWithoutSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithoutSuspectFunctionCall()
    {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithSuspectFunctionCall()
    {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithMultipleSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithMultipleSuspectFunctionCall()
    {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithoutSuspectFunctionCall()
    {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithSuspectFunctionCall()
    {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithMultipleSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithMultipleSuspectFunctionCall()
    {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
