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

namespace PHPMD\Regression;

use PHPMD\Rule\UnusedFormalParameter;
use PHPMD\RuleSet;
use Throwable;

/**
 * Regression test for issue 14990109.
 *
 * @link https://www.pivotaltracker.com/story/show/14990109
 * @since 1.1.0
 */
class UnusedParameterArgvTicket14990109RegressionTest extends AbstractRegressionTestCase
{
    /**
     * testRuleDoesNotApplyToFunctionParameterNamedArgv
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToFunctionParameterNamedArgv(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new UnusedFormalParameter());
        $ruleSet->setReport($this->getReportWithNoViolation());

        $ruleSet->apply($this->getFunction());
    }

    /**
     * testRuleDoesNotApplyToMethodParameterNamedArgv
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToMethodParameterNamedArgv(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new UnusedFormalParameter());
        $ruleSet->setReport($this->getReportWithNoViolation());

        $ruleSet->apply($this->getMethod());
    }
}
