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

namespace PHPMD\Rule\Controversial;

use PHPMD\AbstractTestCase;

/**
 * Test case for the camel case parameter name rule.
 *
 * @covers \PHPMD\Rule\Controversial\CamelCaseParameterName
 */
class CamelCaseParameterNameTest extends AbstractTestCase
{
    /**
     * Tests that the rule does apply for an invalid parameter name
     */
    public function testRuleDoesApplyForInParameterNameWithUnderscore(): void
    {
        $report = $this->getReportWithOneViolation();

        foreach ($this->getClass()->getMethods() as $method) {
            $rule = new CamelCaseParameterName();
            $rule->setReport($report);
            $rule->addProperty('allow-underscore', 'false');
            $rule->apply($method);
        }
    }

    /**
     * Tests that the rule does apply for all caps abbreviation when not allowed
     */
    public function testRuleDoesApplyForAllCapsAbbreviation(): void
    {
        $report = $this->getReportWithOneViolation();

        foreach ($this->getClass()->getMethods() as $method) {
            $rule = new CamelCaseParameterName();
            $rule->setReport($report);
            $rule->addProperty('camelcase-abbreviations', 'true');
            $rule->addProperty('allow-underscore', 'false');
            $rule->apply($method);
        }
    }

    /**
     * Tests that the rule does not apply for camelcase abbreviation
     */
    public function testRuleDoesNotApplyForCamelcaseAbbreviation(): void
    {
        $report = $this->getReportWithNoViolation();

        foreach ($this->getClass()->getMethods() as $method) {
            $rule = new CamelCaseParameterName();
            $rule->setReport($report);
            $rule->addProperty('camelcase-abbreviations', 'true');
            $rule->addProperty('allow-underscore', 'false');
            $rule->apply($method);
        }
    }

    /**
     * Tests that the rule does apply for an invalid parameter name
     * starting with a capital.
     */
    public function testRuleDoesApplyForParameterNameWithCapital(): void
    {
        $report = $this->getReportWithOneViolation();

        foreach ($this->getClass()->getMethods() as $method) {
            $rule = new CamelCaseParameterName();
            $rule->setReport($report);
            $rule->addProperty('allow-underscore', 'false');
            $rule->apply($method);
        }
    }

    /**
     * Tests that the rule does NOT apply for a valid parameter name
     */
    public function testRuleDoesNotApplyForValidParameterName(): void
    {
        $report = $this->getReportWithNoViolation();

        foreach ($this->getClass()->getMethods() as $method) {
            $rule = new CamelCaseParameterName();
            $rule->setReport($report);
            $rule->addProperty('allow-underscore', 'false');
            $rule->apply($method);
        }
    }

    /**
     * Tests that the rule does apply for a valid parameter name
     * with an underscore at the beginning when it is allowed.
     */
    public function testRuleDoesNotApplyForValidParameterNameWithUnderscoreWhenAllowed(): void
    {
        $report = $this->getReportWithNoViolation();

        foreach ($this->getClass()->getMethods() as $method) {
            $rule = new CamelCaseParameterName();
            $rule->setReport($report);
            $rule->addProperty('allow-underscore', 'true');
            $rule->apply($method);
        }
    }
}
