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

use PHPMD\AbstractTestCase;

/**
 * Duplicated Array Key Test.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @author Kamil Szymanaski <kamil.szymanski@gmail.com>
 */
class DuplicatedArrayKeyTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToMethodWithoutArrayDefinition
     */
    public function testRuleNotAppliesToMethodWithoutArrayDefinition(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithNonAssotiativeArrayDefinition
     */
    public function testRuleNotAppliesToMethodWithNonAssotiativeArrayDefinition(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys
     */
    public function testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
     */
    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys
     */
    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys
     */
    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
     */
    public function testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutArrayDefinition
     */
    public function testRuleNotAppliesToFunctionWithoutArrayDefinition(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNonAssotiativeArrayDefinition
     */
    public function testRuleNotAppliesToFunctionWithNonAssotiativeArrayDefinition(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithAssotiativeArrayDefinitionWithoutDuplicatedKeys
     */
    public function testRuleNotAppliesToFunctionWithAssotiativeArrayDefinitionWithoutDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys
     */
    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys
     */
    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys
     */
    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesMultipleTimesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys
     */
    public function testRuleAppliesMultipleTimesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesWhenKeyIsDeclaredInNonStandardWay
     */
    public function testRuleAppliesWhenKeyIsDeclaredInNonStandardWay(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesCorrectlyWithNestedArrays
     */
    public function testRuleAppliesCorrectlyWithNestedArrays(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesCorrectlyToMultipleArrays
     */
    public function testRuleAppliesCorrectlyToMultipleArrays(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getFunction());
    }
}
