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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;
use Throwable;

/**
 * Test case for the {@link \PHPMD\Rule\Design\DevelopmentCodeFragment} class.
 *
 * @covers \PHPMD\Rule\Design\DevelopmentCodeFragment
 * @link https://github.com/phpmd/phpmd/issues/265
 * @since 2.3.0
 */
class DevelopmentCodeFragmentTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToMethodWithoutSuspectFunctionCall
     * @throws Throwable
     */
    public function testRuleNotAppliesToMethodWithoutSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithSuspectFunctionCall
     * @throws Throwable
     */
    public function testRuleAppliesToMethodWithSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithMultipleSuspectFunctionCall
     * @throws Throwable
     */
    public function testRuleAppliesToMethodWithMultipleSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithSuspectFullyQualifiedFunctionCall
     * @throws Throwable
     */
    public function testRuleAppliesToMethodWithSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithMultipleSuspectFullyQualifiedFunctionCall
     * @throws Throwable
     */
    public function testRuleAppliesToMethodWithMultipleSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutSuspectFunctionCall
     * @throws Throwable
     */
    public function testRuleNotAppliesToFunctionWithoutSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithSuspectFunctionCall
     * @throws Throwable
     */
    public function testRuleAppliesToFunctionWithSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithMultipleSuspectFunctionCall
     * @throws Throwable
     */
    public function testRuleAppliesToFunctionWithMultipleSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithSuspectFullyQualifiedFunctionCall
     * @throws Throwable
     */
    public function testRuleAppliesToFunctionWithSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithMultipleSuspectFullyQualifiedFunctionCall
     * @throws Throwable
     */
    public function testRuleAppliesToFunctionWithMultipleSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToMethodWithinNamespace
     * @throws Throwable
     */
    public function testRuleAppliesToMethodWithinNamespace(): void
    {
        $rule = $this->getRule();
        $rule->addProperty('ignore-namespaces', 'true');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToMethodWithinNamespaceByDefault
     * @throws Throwable
     */
    public function testRuleNotAppliesToMethodWithinNamespaceByDefault(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Get a configured DevelopmentCodeFragment rule
     */
    private function getRule(): DevelopmentCodeFragment
    {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->addProperty('ignore-namespaces', 'false');

        return $rule;
    }
}
