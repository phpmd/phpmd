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
 *
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
     *
     * @return void
     */
    public function testRuleAppliesToMethodStartingWithGetAndReturningBoolean()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodStartingWithGetAndReturningBool
     *
     * @return void
     */
    public function testRuleAppliesToMethodStartingWithGetAndReturningBool()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToPearPrivateMethodStartingWithGetAndReturningBoolean
     *
     * @return void
     */
    public function testRuleAppliesToPearPrivateMethodStartingWithGetAndReturningBoolean()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleIgnoresParametersWhenNotExplicitConfigured
     *
     * @return void
     */
    public function testRuleIgnoresParametersWhenNotExplicitConfigured()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesWhenParametersAreExplicitEnabled
     *
     * @return void
     */
    public function testRuleNotAppliesWhenParametersAreExplicitEnabled()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'true');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodStartingWithIs
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodStartingWithIs()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodStartingWithHas
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodStartingWithHas()
    {
        $rule = new BooleanGetMethodName();
        $rule->addProperty('checkParameterizedMethods', 'false');
        $rule->setReport($this->getReportWithNoViolation());

        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithReturnTypeNotBoolean
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithReturnTypeNotBoolean()
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
