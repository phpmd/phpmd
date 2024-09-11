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
 * Test case for the camel case property name rule.
 *
 * @covers \PHPMD\Rule\Controversial\CamelCasePropertyName
 */
class CamelCasePropertyNameTest extends AbstractTestCase
{
    /**
     * Tests that the rule does not apply for a valid property name.
     */
    public function testRuleDoesNotApplyForValidPropertyName(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCasePropertyName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does apply for all caps abbreviation in property name.
     */
    public function testRuleDoesApplyForAllCapsAbbreviationInProperty(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCasePropertyName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does not apply for a camelcase abbreviation in property name.
     */
    public function testRuleDoesNotApplyForCamelcaseAbbreviationInProperty(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCasePropertyName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does apply for a property name
     * starting with a capital.
     */
    public function testRuleDoesApplyForPropertyNameWithCapital(): void
    {
        // Test property name with capital at the beginning
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCasePropertyName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does apply for a property name
     * with underscores.
     */
    public function testRuleDoesApplyForPropertyNameWithUnderscores(): void
    {
        // Test property name with underscores
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCasePropertyName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does apply for a valid property name
     * with an underscore at the beginning when it is allowed.
     */
    public function testRuleDoesApplyForValidPropertyNameWithUnderscoreWhenNotAllowed(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCasePropertyName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does not apply for a valid property name
     * with no underscore at the beginning when it is allowed.
     */
    public function testRuleDoesNotApplyForValidPropertyNameWithNoUnderscoreWhenAllowed(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCasePropertyName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'true');
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does not apply for a valid property name
     * with an underscore at the beginning when it is allowed.
     */
    public function testRuleDoesNotApplyForValidPropertyNameWithUnderscoreWhenAllowed(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCasePropertyName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'true');
        $rule->apply($this->getClass());
    }
}
