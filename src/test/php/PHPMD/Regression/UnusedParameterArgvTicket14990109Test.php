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
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link       https://www.pivotaltracker.com/story/show/14990109
 */

namespace PHPMD\Regression;

use PHPMD\Rule\UnusedFormalParameter;
use PHPMD\RuleSet;

/**
 * Regression test for issue 14990109.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link       https://www.pivotaltracker.com/story/show/14990109
 * @since      1.1.0
 *
 * @ticket 14990109
 * @covers \stdClass
 * @group phpmd
 * @group phpmd::regression
 * @group regressiontest
 */
class UnusedParameterArgvTicket14990109Test extends AbstractTest
{
    /**
     * testRuleDoesNotApplyToFunctionParameterNamedArgv
     *
     * @return void
     */
    public function testRuleDoesNotApplyToFunctionParameterNamedArgv()
    {
        $rule = new UnusedFormalParameter();
        $rule->addProperty('allow-unused-before-used', 'false');

        $ruleSet = new RuleSet();
        $ruleSet->addRule($rule);
        $ruleSet->setReport($this->getReportMock(0));

        $ruleSet->apply($this->getFunction());
    }

    /**
     * testRuleDoesNotApplyToMethodParameterNamedArgv
     *
     * @return void
     */
    public function testRuleDoesNotApplyToMethodParameterNamedArgv()
    {
        $rule = new UnusedFormalParameter();
        $rule->addProperty('allow-unused-before-used', 'false');

        $ruleSet = new RuleSet();
        $ruleSet->addRule($rule);
        $ruleSet->setReport($this->getReportMock(0));

        $ruleSet->apply($this->getMethod());
    }
}
