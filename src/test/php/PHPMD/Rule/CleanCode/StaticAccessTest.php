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
 * @coversDefaultClass \PHPMD\Rule\CleanCode\StaticAccess
 */
class StaticAccessTest extends AbstractTest
{
    public function testRuleNotAppliesToParentStaticCall()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToSelfStaticCall()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToDynamicMethodCall()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToStaticMethodAccessWhenExcluded()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('exceptions', 'Excluded1,Excluded2');
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToStaticMethodAccessWhenExcludedViaWildCard1()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('exceptions', 'Illuminate\Support\*');
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToStaticMethodAccessWhenExcludedViaWildCard2()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('exceptions', '*\Support\*');
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToStaticMethodAccessWhenExcludedViaWildCard3()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('exceptions', 'Illuminate\*');
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToStaticMethodAccess()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToStaticMethodAccessWhenNotAllExcluded()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('exceptions', 'Excluded');
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToConstantAccess()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @covers ::apply
     * @covers ::isMethodIgnored
     */
    public function testRuleNotAppliesToStaticMethodAccessWhenIgnored()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('ignorepattern', '/^create/');
        $rule->apply($this->getMethod());
    }

    /**
     * @covers ::apply
     * @covers ::isMethodIgnored
     */
    public function testRuleAppliesToStaticMethodAccessWhenNotIgnored()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('ignorepattern', '/^foobar/');
        $rule->apply($this->getMethod());
    }
}
