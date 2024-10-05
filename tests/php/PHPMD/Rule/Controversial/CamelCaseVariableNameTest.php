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
 * Test case for the camel case variable name rule.
 *
 * @covers \PHPMD\Rule\Controversial\CamelCaseVariableName
 */
class CamelCaseVariableNameTest extends AbstractTestCase
{
    /**
     * Tests that the rule does apply for an invalid variable name
     */
    public function testRuleDoesApplyForInvariableNameWithUnderscore(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseVariableName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does apply for variable name
     * with all caps abbreviation.
     */
    public function testRuleDoesApplyForAllCapsAbbreviation(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseVariableName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does not apply for variable name
     * with camelcase abbreviation.
     */
    public function testRuleDoesNotApplyForCamelcaseAbbreviation(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseVariableName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does apply for an invalid variable name
     * starting with a capital.
     */
    public function testRuleDoesApplyForVariableNameWithCapital(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseVariableName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does NOT apply for a valid variable name
     */
    public function testRuleDoesNotApplyForValidVariableName(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseVariableName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does NOT apply for a statically accessed variable
     */
    public function testRuleDoesNotApplyForStaticVariableAccess(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseVariableName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does NOT apply if name allowed by config
     */
    public function testRuleDoesNotApplyIfExcluded(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseVariableName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does apply for a valid variable name
     * with an underscore at the beginning when it is allowed.
     */
    public function testRuleDoesNotApplyForValidVariableNameWithUnderscoreWhenAllowed(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseVariableName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'true');
        $rule->apply($this->getClass());
    }
}
