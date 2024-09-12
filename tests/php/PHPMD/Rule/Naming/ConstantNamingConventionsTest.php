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
use Throwable;

/**
 * Test case for the constructor name rule.
 *
 * @covers \PHPMD\Rule\Naming\ConstantNamingConventions
 */
class ConstantNamingConventionsTest extends AbstractTestCase
{
    /**
     * testRuleAppliesToClassConstantWithLowerCaseCharacters
     * @throws Throwable
     */
    public function testRuleAppliesToClassConstantWithLowerCaseCharacters(): void
    {
        $rule = new ConstantNamingConventions();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getClass());
    }

    /**
     * testRuleAppliesToInterfaceConstantWithLowerCaseCharacters
     * @throws Throwable
     */
    public function testRuleAppliesToInterfaceConstantWithLowerCaseCharacters(): void
    {
        $rule = new ConstantNamingConventions();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getInterface());
    }

    /**
     * testRuleNotAppliesToClassConstantWithUpperCaseCharacters
     * @throws Throwable
     */
    public function testRuleNotAppliesToClassConstantWithUpperCaseCharacters(): void
    {
        $rule = new ConstantNamingConventions();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesToInterfaceConstantWithUpperCaseCharacters
     * @throws Throwable
     */
    public function testRuleNotAppliesToInterfaceConstantWithUpperCaseCharacters(): void
    {
        $rule = new ConstantNamingConventions();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getInterface());
    }
}
