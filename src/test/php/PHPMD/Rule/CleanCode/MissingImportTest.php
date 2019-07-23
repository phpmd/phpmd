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
    public function testRuleNotAppliesToClassWithoutAnyDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToClassWithOnlyImportedDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToClassWithNotImportedDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToClassWithSelfAndStaticCalls()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToFunctionWithoutAnyDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    public function testRuleNotAppliesToFunctionWithOnlyImportedDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToFunctionWithNotImportedDependencies()
    {
        $rule = new MissingImport();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }
}