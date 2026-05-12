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
    public function testRuleNotAppliesToMethodWithoutArrayDefinition(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToMethodWithNonAssotiativeArrayDefinition(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToFunctionWithoutArrayDefinition(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToFunctionWithNonAssotiativeArrayDefinition(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToFunctionWithAssotiativeArrayDefinitionWithoutDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesMultipleTimesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesWhenKeyIsDeclaredInNonStandardWay(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesCorrectlyWithNestedArrays(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesCorrectlyToMultipleArrays(): void
    {
        $rule = new DuplicatedArrayKey();
        $rule->setReport($this->getReportMock(4));
        $rule->apply($this->getFunction());
    }
}
