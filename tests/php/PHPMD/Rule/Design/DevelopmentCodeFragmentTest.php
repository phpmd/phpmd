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
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test case for the {@link \PHPMD\Rule\Design\DevelopmentCodeFragment} class.
 *
 * @link https://github.com/phpmd/phpmd/issues/265
 * @since 2.3.0
 */
#[CoversClass(DevelopmentCodeFragment::class)]
class DevelopmentCodeFragmentTest extends AbstractTestCase
{
    public function testRuleNotAppliesToMethodWithoutSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithMultipleSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithMultipleSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToFunctionWithoutSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithMultipleSuspectFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithMultipleSuspectFullyQualifiedFunctionCall(): void
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToMethodWithinNamespace(): void
    {
        $rule = $this->getRule();
        $rule->addProperty('ignore-namespaces', 'true');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getClass());
    }

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
