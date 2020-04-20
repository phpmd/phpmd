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

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractTest;

/**
 * Test case for the undefined variable rule.
 *
 * @coversDefaultClass \PHPMD\Rule\CleanCode\UndefinedVariable
 */
class UndefinedVariableTest extends AbstractTest
{
    /**
     * testRuleAppliesToUndefinedVariable
     *
     * @return void
     */
    public function testRuleAppliesToUndefinedVariable()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToUndefinedVariableWithDefinedVariable
     *
     * @return void
     */
    public function testRuleAppliesToUndefinedVariableWithDefinedVariable()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToUndefinedVariableOnArray
     *
     * @return void
     */
    public function testRuleAppliesToUndefinedVariableOnArray()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToUndefinedVariableOnArrayWithKeys
     *
     * @return void
     */
    public function testRuleAppliesToUndefinedVariableOnArrayWithKeys()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToUndefinedVariableOnArrayWithKeys
     *
     * @return void
     */
    public function testRuleDoesNotApplyToSuperGlobals()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToUsedProperties
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUsedProperties()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToReferences
     *
     * @return void
     */
    public function testRuleDoesNotApplyToReferences()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToKeyValuePair
     *
     * @return void
     */
    public function testRuleDoesNotApplyToKeyValuePair()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToKeyReferencePair
     *
     * @return void
     */
    public function testRuleDoesNotApplyToKeyReferencePair()
    {
        $rule = new UndefinedVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}
