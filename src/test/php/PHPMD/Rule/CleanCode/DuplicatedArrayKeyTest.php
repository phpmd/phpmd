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
 * Duplicated Array Key Test.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @author Kamil Szymanaski <kamil.szymanski@gmail.com>
 */
class DuplicatedArrayKeyTest extends AbstractTest
{
    /**
     * testRuleNotAppliesToMethodWithoutArrayDefinition
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithoutArrayDefinition()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNonAssotiativeArrayDefinition
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithNonAssotiativeArrayDefinition()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
     *
     * @return void
     */
    public function testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutArrayDefinition
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithoutArrayDefinition()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNonAssotiativeArrayDefinition
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithNonAssotiativeArrayDefinition()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithAssotiativeArrayDefinitionWithoutDuplicatedKeys
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithAssotiativeArrayDefinitionWithoutDuplicatedKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesMultipleTimesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys
     *
     * @return void
     */
    public function testRuleAppliesMultipleTimesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesWhenKeyIsDeclaredInNonStandardWay
     *
     * @return void
     */
    public function testRuleAppliesWhenKeyIsDeclaredInNonStandardWay()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesCorrectlyWithNestedArrays
     *
     * @return void
     */
    public function testRuleAppliesCorrectlyWithNestedArrays()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesCorrectlyToMultipleArrays
     *
     * @return void
     */
    public function testRuleAppliesCorrectlyToMultipleArrays()
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getFunction());
    }
}
