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
 * MissingImport Tests
 *
 * @coversDefaultClass \PHPMD\Rule\CleanCode\MissingImport
 */
class MissingImportTest extends AbstractTest
{
    /**
     * Tests that it does not apply to a class without any class dependencies
     *
     * @return void
     * @covers ::apply
     */
    public function testRuleNotAppliesToClassWithoutAnyDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that it does not apply to a class with only imported classes
     *
     * @return void
     * @covers ::apply
     * @covers ::isSelfReference
     */
    public function testRuleNotAppliesToClassWithOnlyImportedDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that it applies to a class that has fully qualified class names
     *
     * @return void
     * @covers ::apply
     * @covers ::isSelfReference
     */
    public function testRuleAppliesToClassWithNotImportedDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that it does not apply to a class that uses self references
     *
     * @return void
     * @covers ::apply
     * @covers ::isSelfReference
     */
    public function testRuleNotAppliesToClassWithSelfAndStaticCalls()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * Tests that it does not apply to a function without any class dependencies
     *
     * @return void
     * @covers ::apply
     */
    public function testRuleNotAppliesToFunctionWithoutAnyDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that it does not apply to a function with only imported classes
     *
     * @return void
     * @covers ::apply
     * @covers ::isSelfReference
     */
    public function testRuleNotAppliesToFunctionWithOnlyImportedDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that it applies to a function that has fully qualified class names
     *
     * @return void
     * @covers ::apply
     * @covers ::isSelfReference
     */
    public function testRuleAppliesToFunctionWithNotImportedDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }
}