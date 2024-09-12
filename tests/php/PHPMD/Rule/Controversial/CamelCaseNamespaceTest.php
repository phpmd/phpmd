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
use Throwable;

/**
 * Test case for the camel case namespace rule.
 * @covers \PHPMD\Rule\Controversial\CamelCaseNamespace
 */
class CamelCaseNamespaceTest extends AbstractTestCase
{
    /**
     * Rule does not apply for valid namespace.
     * @throws Throwable
     */
    public function testRuleDoesNotApplyForValidNamespace(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseNamespace();
        $rule->setReport($report);
        $rule->apply($this->getClass());
    }

    /**
     * Rule does apply for incorrect namespace.
     * @throws Throwable
     */
    public function testRuleDoesApplyForIncorrectNamespace(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseNamespace();
        $rule->setReport($report);
        $rule->apply($this->getClass());
    }

    /**
     * Rule does not apply for namespace with uppercase abbreviation.
     * @throws Throwable
     */
    public function testRuleDoesNotApplyForNamespaceWithUppercaseAbbreviation(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseNamespace();
        $rule->setReport($report);
        $rule->apply($this->getClass());
    }

    /**
     * Rule does apply for namespace with uppercase abbreviation.
     * @throws Throwable
     */
    public function testRuleDoesApplyForNamespaceWithUppercaseAbbreviation(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseNamespace();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->apply($this->getClass());
    }

    /**
     * Rule does not apply for invalid namespace in exception list.
     * @throws Throwable
     */
    public function testRuleDoesNotApplyForNamespaceInException(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseNamespace();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->addProperty('exceptions', 'URL');
        $rule->apply($this->getClass());
    }
}
