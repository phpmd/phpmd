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

use PHPMD\Rule\UnusedPrivateMethod;
use PHPMD\RuleSet;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Regression test for issues 252, 558, and 937.
 * https://github.com/phpmd/phpmd/issues/252
 * https://github.com/phpmd/phpmd/issues/558
 * https://github.com/phpmd/phpmd/issues/937
 */
#[CoversClass(UnusedPrivateMethod::class)]
class UnusedPrivateMethodFalsePositiveTicket937Test extends AbstractRegressionTestCase
{
    /**
     * Private method called on clone of $this should not be flagged as unused.
     */
    public function testRuleDoesNotApplyToPrivateMethodCalledOnClone(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new UnusedPrivateMethod());
        $ruleSet->setReport($this->getReportWithNoViolation());

        $ruleSet->apply($this->getClass());
    }

    /**
     * Private method called on new self should not be flagged as unused.
     */
    public function testRuleDoesNotApplyToPrivateMethodCalledOnNewSelf(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new UnusedPrivateMethod());
        $ruleSet->setReport($this->getReportWithNoViolation());

        $ruleSet->apply($this->getClass());
    }

    /**
     * Private method called on new static should not be flagged as unused.
     */
    public function testRuleDoesNotApplyToPrivateMethodCalledOnNewStatic(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new UnusedPrivateMethod());
        $ruleSet->setReport($this->getReportWithNoViolation());

        $ruleSet->apply($this->getClass());
    }
}
