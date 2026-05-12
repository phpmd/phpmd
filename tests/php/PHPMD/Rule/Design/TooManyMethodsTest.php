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
use PHPMD\Node\ClassNode;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test case for the too many methods rule.
 */
#[CoversClass(TooManyMethods::class)]
class TooManyMethodsTest extends AbstractTestCase
{
    public function testRuleDoesNotApplyToClassesWithLessMethodsThanThreshold(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(23));
    }

    public function testRuleDoesNotApplyToClassesWithSameNumberOfMethodsAsThreshold(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42));
    }

    public function testRuleAppliesToClassesWithMoreMethodsThanThreshold(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('maxmethods', '23');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42, array_fill(0, 42, __FUNCTION__)));
    }

    public function testRuleIgnoresGetterMethodsInTest(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'getClass']));
    }

    public function testRuleIgnoresSetterMethodsInTest(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'setClass']));
    }

    public function testRuleIgnoresCustomMethodsWhenRegexPropertyIsGiven(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'injectClass']));
    }

    public function testRuleIgnoresGetterAndSetterMethodsInTest(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '2');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(3, ['invoke', 'getClass', 'setClass']));
    }

    public function testRuleIgnoresHassers(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'hasClass']));
    }

    public function testRuleIgnoresIssers(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'isClass']));
    }

    public function testRuleIgnoresWithers(): void
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'withClass']));
    }

    /**
     * Creates a prepared class node mock
     *
     * @param string[] $methodNames
     */
    private function createClassMock(int $numberOfMethods, ?array $methodNames = null): ClassNode
    {
        $class = $this->getClassMock('nom', $numberOfMethods);

        if (is_array($methodNames)) {
            $class->expects(static::once())
                ->method('getMethodNames')
                ->willReturn($methodNames);
        }

        return $class;
    }
}
