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

use PHPMD\AbstractTest;

/**
 * Test case for the camel case method name rule.
 *
 * @covers \PHPMD\Rule\Controversial\CamelCaseMethodName
 */
class CamelCaseMethodNameTest extends AbstractTest
{
    /**
     * Tests that the rule does not apply for a valid method name.
     *
     * @return void
     */
    public function testRuleDoesNotApplyForValidMethodName()
    {
        //$method = $this->getMethod();
        $report = $this->getReportMock(0);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->addProperty('allow-multiple-underscores-test', 'false');
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that the rule does apply for an method name
     * starting with a capital.
     *
     * @return void
     */
    public function testRuleDoesApplyForMethodNameWithCapital()
    {
        // Test method name with capital at the beginning
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->addProperty('allow-multiple-underscores-test', 'false');

        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a method name
     * with underscores.
     *
     * @return void
     */
    public function testRuleDoesApplyForMethodNameWithUnderscores()
    {
        // Test method name with underscores
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->addProperty('allow-multiple-underscores-test', 'false');

        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a valid method name
     * with an underscore at the beginning when it is allowed.
     *
     * @return void
     */
    public function testRuleDoesApplyForValidMethodNameWithUnderscoreWhenNotAllowed()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->addProperty('allow-multiple-underscores-test', 'false');

        $rule->apply($method);
    }

    /**
     * Tests that the rule does not apply for a valid method name
     * with an underscore at the beginning when it is not allowed.
     *
     * @return void
     */
    public function testRuleDoesNotApplyForValidMethodNameWithUnderscoreWhenAllowed()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(0);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'true');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->addProperty('allow-multiple-underscores-test', 'false');

        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a valid test method name
     * with an underscore.
     *
     * @return void
     */
    public function testRuleDoesApplyForTestMethodWithUnderscoreWhenNotAllowed()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->addProperty('allow-multiple-underscores-test', 'false');

        $rule->apply($method);
    }

    /**
     * Tests that the rule does not apply for a valid test method name
     * with an underscore when an single underscore is allowed.
     *
     * @return void
     */
    public function testRuleDoesNotApplyForTestMethodWithUnderscoreWhenAllowed()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(0);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'true');
        $rule->addProperty('allow-multiple-underscores-test', 'true');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply for a test method name
     * with multiple underscores even when one is allowed.
     *
     * @return void
     */
    public function testRuleAppliesToTestMethodWithTwoUnderscoresEvenWhenOneIsAllowed()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'true');
        $rule->addProperty('allow-multiple-underscores-test', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply to for test method names that
     * have a capital after their single allowed underscore.
     *
     * @return void
     */
    public function testRuleAppliesToTestMethodWithUnderscoreFollowedByCapital()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'true');
        $rule->addProperty('allow-multiple-underscores-test', 'false');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply to for test method names that
     * have multiple underscore in the declaration of test
     *
     * @return void
     */
    public function testRuleAppliesToTestMethodWithMultipleUnderscores()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(0);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->addProperty('allow-multiple-underscores-test', 'true');
        $rule->apply($method);
    }

    /**
     * Tests that the rule does apply to for test method names that
     * have multiple underscore in the declaration of test
     *
     * @return void
     */
    public function testRuleDoesNotApplyForTestMethodWithMultipleUnderscoreWhenAllowed()
    {
        $method = $this->getMethod();
        $report = $this->getReportMock(1);

        $rule = new CamelCaseMethodName();
        $rule->setReport($report);
        $rule->addProperty('allow-underscore', 'false');
        $rule->addProperty('allow-underscore-test', 'false');
        $rule->addProperty('allow-multiple-underscores-test', 'false');
        $rule->apply($method);
    }

    /**
     * Returns the first method found in a source file related to the calling
     * test method.
     *
     * @return \PHPMD\Node\MethodNode
     */
    protected function getMethod()
    {
        $methods = $this->getClass()->getMethods();
        return reset($methods);
    }
}
