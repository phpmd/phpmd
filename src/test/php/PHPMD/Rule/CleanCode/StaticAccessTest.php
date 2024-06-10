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
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Rule\CleanCode\StaticAccess
 */
class StaticAccessTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToParentStaticCall(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToSelfStaticCall(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToDynamicMethodCall(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToStaticMethodAccessWhenExcluded(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('exceptions', 'Excluded1,Excluded2');
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesToStaticMethodAccess(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesToStaticMethodAccessWhenNotAllExcluded(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('exceptions', 'Excluded');
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToConstantAccess(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     * @covers ::apply
     * @covers ::isMethodIgnored
     */
    public function testRuleNotAppliesToStaticMethodAccessWhenIgnored(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('ignorepattern', '/^create/');
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     * @covers ::apply
     * @covers ::isMethodIgnored
     */
    public function testRuleAppliesToStaticMethodAccessWhenNotIgnored(): void
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('ignorepattern', '/^foobar/');
        $rule->apply($this->getMethod());
    }
}
