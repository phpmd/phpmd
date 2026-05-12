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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test case for the {@link \PHPMD\Rule\Design\NumberOfChildren} class.
 */
#[CoversClass(NumberOfChildren::class)]
class NumberOfChildrenTest extends AbstractTestCase
{
    public function testRuleNotAppliesToClassWithChildrenLessThanThreshold(): void
    {
        $rule = new NumberOfChildren();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('nocc', 41));
    }

    public function testRuleAppliesToClassWithChildrenIdenticalToThreshold(): void
    {
        $rule = new NumberOfChildren();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('nocc', 42));
    }

    public function testRuleAppliesToClassWithChildrenGreaterThanThreshold(): void
    {
        $rule = new NumberOfChildren();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('nocc', 43));
    }
}
