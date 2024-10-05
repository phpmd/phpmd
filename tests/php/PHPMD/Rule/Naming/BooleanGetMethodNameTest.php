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
use Throwable;

/**
 * Test case for the {@link \PHPMD\Rule\Naming\BooleanGetMethodName} rule class.
 *
 * @covers \PHPMD\Rule\Naming\BooleanGetMethodName
 */
class BooleanGetMethodNameTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToMethodStartingWithGetAndReturningBoolean
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
     */
    public function testRuleNotAppliesToMethodWithReturnTypeNotBoolean(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToReturnDeclarationBool
     *
     * @throws Throwable
     */
    public function testRuleAppliesToReturnDeclarationBool(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToReturnDeclarationTrue
     *
     * @requires PHP 8.2.0
     * @throws Throwable
     */
    public function testRuleAppliesToReturnDeclarationTrue(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToReturnDeclarationFalse
     *
     * @requires PHP 8.2.0
     * @throws Throwable
     */
    public function testRuleAppliesToReturnDeclarationFalse(): void
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * Returns the first method found in a source file related to the calling
     * test method.
     *
     * @throws Throwable
     */
    protected function getMethod(): MethodNode
    {
        $methods = $this->getClass()->getMethods();
        $method = reset($methods);
        static::assertNotFalse($method);

        return $method;
    }
}
