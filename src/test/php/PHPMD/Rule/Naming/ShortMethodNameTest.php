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
 * Test case for the very short method and function name rule.
 *_Naming
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers PHPMD\Rule\Naming\ShortMethodName
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::naming
 * @group unittest
 */
class ShortMethodNameTest extends AbstractTest
{
    /**
     * testRuleAppliesToFunctionWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithNameShorterThanThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 54);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithNameEqualToThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithNameLongerThanThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 54);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }
    /**
     * testRuleAppliesToFunctionWithNameShorterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithNameShorterThanThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNameEqualToThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithNameEqualToThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 50);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNameLongerThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithNameLongerThanThreshold()
    {
        $rule = new ShortMethodName();
        $rule->addProperty('minimum', 52);
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}
