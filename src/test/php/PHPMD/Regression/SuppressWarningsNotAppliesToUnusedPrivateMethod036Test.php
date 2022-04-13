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

/**
 * Regression test for issue 036.
 *
 * @covers stdClass
 */
class SuppressWarningsNotAppliesToUnusedPrivateMethod036Test extends AbstractTest
{
    /**
     * testRuleDoesNotApplyToPrivateMethodWithSuppressWarningsAnnotation
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPrivateMethodWithSuppressWarningsAnnotation()
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new UnusedPrivateMethod());
        $ruleSet->setReport($this->getReportWithNoViolation());

        $ruleSet->apply($this->getClass());
    }
}
