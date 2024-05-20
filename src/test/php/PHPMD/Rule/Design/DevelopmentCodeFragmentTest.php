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
     */
    public function testRuleNotAppliesToMethodWithoutSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithSuspectFunctionCall
     */
    public function testRuleAppliesToMethodWithSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithMultipleSuspectFunctionCall
     */
    public function testRuleAppliesToMethodWithMultipleSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithSuspectFullyQualifiedFunctionCall
     */
    public function testRuleAppliesToMethodWithSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithMultipleSuspectFullyQualifiedFunctionCall
     */
    public function testRuleAppliesToMethodWithMultipleSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutSuspectFunctionCall
     */
    public function testRuleNotAppliesToFunctionWithoutSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithSuspectFunctionCall
     */
    public function testRuleAppliesToFunctionWithSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithMultipleSuspectFunctionCall
     */
    public function testRuleAppliesToFunctionWithMultipleSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithSuspectFullyQualifiedFunctionCall
     */
    public function testRuleAppliesToFunctionWithSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithMultipleSuspectFullyQualifiedFunctionCall
     */
    public function testRuleAppliesToFunctionWithMultipleSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToMethodWithinNamespace
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
     */
    public function testRuleNotAppliesToMethodWithinNamespaceByDefault(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * Get a configured DevelopmentCodeFragment rule
     *
     * @return DevelopmentCodeFragment
     */
    private function getRule()
    {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->addProperty('ignore-namespaces', 'false');

        return $rule;
    }
}
