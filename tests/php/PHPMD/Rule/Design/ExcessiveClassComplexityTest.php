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
 * Test case for the weighted method count rule.
 *
 * @since 0.2.5
 */
#[CoversClass(ExcessiveClassComplexity::class)]
class ExcessiveClassComplexityTest extends AbstractTestCase
{
    public function testRuleAppliesForValueGreaterThanThreshold(): void
    {
        $class = $this->getClassMock('wmc', 42);
        $report = $this->getReportWithOneViolation();

        $rule = new ExcessiveClassComplexity();
        $rule->setReport($report);
        $rule->addProperty('maximum', '10');
        $rule->apply($class);
    }

    public function testRuleAppliesForValueEqualToThreshold(): void
    {
        $class = $this->getClassMock('wmc', 42);
        $report = $this->getReportWithOneViolation();

        $rule = new ExcessiveClassComplexity();
        $rule->setReport($report);
        $rule->addProperty('maximum', '42');
        $rule->apply($class);
    }

    public function testRuleNotAppliesForValueLowerThanThreshold(): void
    {
        $class = $this->getClassMock('wmc', 42);
        $report = $this->getReportWithNoViolation();

        $rule = new ExcessiveClassComplexity();
        $rule->setReport($report);
        $rule->addProperty('maximum', '43');
        $rule->apply($class);
    }
}
