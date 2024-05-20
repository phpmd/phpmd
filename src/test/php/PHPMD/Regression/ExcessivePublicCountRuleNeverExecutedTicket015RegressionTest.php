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

use PHPMD\Rule\ExcessivePublicCount;
use PHPMD\RuleSet;

/**
 * Regression test for issue 015.
 */
class ExcessivePublicCountRuleNeverExecutedTicket015RegressionTest extends AbstractRegressionTestCase
{
    /**
     * testRuleSetInvokesRuleForClassInstance
     */
    public function testRuleSetInvokesRuleForClassInstance(): void
    {
        $rule = new ExcessivePublicCount();
        $rule->addProperty('minimum', 3);

        $class = $this->getClass();
        $class->setMetrics(['cis' => 4]);

        $ruleSet = new RuleSet();
        $ruleSet->addRule($rule);
        $ruleSet->setReport($this->getReportWithOneViolation());

        $ruleSet->apply($class);
    }
}
