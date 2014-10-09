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
 *_Design
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule\Controversial;

use PHPMD\AbstractTest;

/**
 * Test case for the camel case method name rule.
 *_Design
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Rule\Controversial\CamelCaseMethodName
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::controversial
 * @group unittest
 */
class CamelCaseMethodNameTest extends AbstractTest
{
    /**
     * Tests that the rule does not apply for a valid method name.
     *
     * @return void
     */
    public function testRuleDoesNotApplyForValidMethodName()
    {
        //$method = $this->getMethod();
        $report = $this->getReportMock(0);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that the rule does apply for an method name
     * starting with a capital.
     *
     * @return void
     */
    public function testRuleDoesApplyForMethodNameWithCapital()
    {
        // Test method name with capital at the beginning
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a method name
     * with underscores.
     *
     * @return void
     */
    public function testRuleDoesApplyForMethodNameWithUnderscores()
    {
        // Test method name with underscores
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a valid method name
     * with an underscore at the beginning when it is allowed.
     *
     * @return void
     */
    public function testRuleDoesApplyForValidMethodNameWithUnderscoreWhenNotAllowed()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does not apply for a valid method name
     * with an underscore at the beginning when it is not allowed.
     *
     * @return void
     */
    public function testRuleDoesNotApplyForValidMethodNameWithUnderscoreWhenAllowed()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(0);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'true');
        $rule->apply($method);
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
