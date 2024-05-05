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

namespace PHPMD\Rule\Naming;

use PHPMD\AbstractTestCase;
use PHPMD\Node\MethodNode;

/**
 * Test case for the {@link \PHPMD\Rule\Naming\BooleanGetMethodName} rule class.
 *
 * @covers \PHPMD\Rule\Naming\BooleanGetMethodName
 */
class BooleanGetMethodNameTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToMethodStartingWithGetAndReturningBoolean
     */
    public function testRuleAppliesToMethodStartingWithGetAndReturningBoolean(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodStartingWithGetAndReturningBool
     */
    public function testRuleAppliesToMethodStartingWithGetAndReturningBool(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToPearPrivateMethodStartingWithGetAndReturningBoolean
     */
    public function testRuleAppliesToPearPrivateMethodStartingWithGetAndReturningBoolean(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleIgnoresParametersWhenNotExplicitConfigured
     */
    public function testRuleIgnoresParametersWhenNotExplicitConfigured(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesWhenParametersAreExplicitEnabled
     */
    public function testRuleNotAppliesWhenParametersAreExplicitEnabled(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'true');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodStartingWithIs
     */
    public function testRuleNotAppliesToMethodStartingWithIs(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodStartingWithHas
     */
    public function testRuleNotAppliesToMethodStartingWithHas(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithReturnTypeNotBoolean
     */
    public function testRuleNotAppliesToMethodWithReturnTypeNotBoolean(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getMethod());
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
