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
use PHPMD\Node\MethodNode;

/**
 * Test case for the camel case method name rule.
 *
 * @covers \PHPMD\Rule\Controversial\CamelCaseMethodName
 */
class CamelCaseMethodNameTest extends AbstractTestCase
{
    /**
     * Tests that the rule does not apply for a valid method name.
     */
    public function testRuleDoesNotApplyForValidMethodName(): void
    {
        //$method = $this->getMethod();
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that the rule does apply for method name
     * with all caps abbreviation.
     */
    public function testRuleDoesApplyForMethodNameWithAllCapsAbbreviation(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that the rule does not apply for method name
     * with camelcase abbreviation.
     */
    public function testRuleDoesNotApplyForMethodNameWithCamelcaseAbbreviation(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that the rule does apply for an method name
     * starting with a capital.
     */
    public function testRuleDoesApplyForMethodNameWithCapital(): void
    {
        // Test method name with capital at the beginning
        $method = $this->getMethod();
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a method name
     * with underscores.
     */
    public function testRuleDoesApplyForMethodNameWithUnderscores(): void
    {
        // Test method name with underscores
        $method = $this->getMethod();
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a valid method name
     * with an underscore at the beginning when it is allowed.
     */
    public function testRuleDoesApplyForValidMethodNameWithUnderscoreWhenNotAllowed(): void
    {
        $method = $this->getMethod();
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does not apply for a valid method name
     * with an underscore at the beginning when it is not allowed.
     */
    public function testRuleDoesNotApplyForValidMethodNameWithUnderscoreWhenAllowed(): void
    {
        $method = $this->getMethod();
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'true');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does not apply for a valid method name
     * with an underscore at the beginning when it is not allowed.
     */
    public function testRuleDoesNotApplyForMagicMethods(): void
    {
        $methods = $this->getClass()->getMethods();

        foreach ($methods as $method) {
            $report = $this->getReportMock($method->getName() === '__notAllowed' ? 1 : 0);

            $rule = new CamelCaseMethodName();
            $rule->setReport($report);
            $rule->addProperty('allow-underscore', 'false');
            $rule->addProperty('allow-underscore-test', 'false');
            $rule->apply($method);
        }
    }

    /**
     * Tests that the rule does apply for a valid test method name
     * with an underscore.
     */
    public function testRuleDoesApplyForTestMethodWithUnderscoreWhenNotAllowed(): void
    {
        $method = $this->getMethod();
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does not apply for a valid test method name
     * with an underscore when underscores are allowed.
     */
    public function testRuleDoesNotApplyForTestMethodWithUnderscoreWhenAllowed(): void
    {
        $method = $this->getMethod();
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'true');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does not apply for a valid test method name
     * with multiple underscores in different positions when underscores are allowed.
     */
    public function testRuleDoesNotApplyForTestMethodWithMultipleUnderscoresWhenAllowed(): void
    {
        $method = $this->getMethod();
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'true');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a test method name
     * with consecutive underscores even when underscores are allowed.
     */
    public function testRuleAppliesToTestMethodWithTwoConsecutiveUnderscoresWhenAllowed(): void
    {
        $method = $this->getMethod();
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'true');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply to for test method names that
     * have a capital after their single allowed underscore.
     */
    public function testRuleAppliesToTestMethodWithUnderscoreFollowedByCapital(): void
    {
        $method = $this->getMethod();
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'true');
        $rule->apply($method);
    }

    /**
     * Returns the first method found in a source file related to the calling
     * test method.
     *
     * @return MethodNode
     */
    protected function getMethod()
    {
        $methods = $this->getClass()->getMethods();

        return reset($methods);
    }
}
