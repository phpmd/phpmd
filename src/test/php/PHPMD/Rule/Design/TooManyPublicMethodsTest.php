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

use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\State;
use PHPMD\AbstractTestCase;
use PHPMD\Node\ClassNode;
use PHPMD\Node\MethodNode;
use PHPMD\Report;
use Throwable;

/**
 * Test case for the too many public methods rule.
 *
 * @covers \PHPMD\Rule\Design\TooManyPublicMethods
 */
class TooManyPublicMethodsTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToClassesWithLessMethodsThanThreshold(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(23));
    }

    /**
     * @throws Throwable
     */
    public function testRuleDoesNotApplyToClassesWithSameNumberOfMethodsAsThreshold(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42));
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesToClassesWithMoreMethodsThanThreshold(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('maxmethods', '23');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42, array_fill(0, 42, __FUNCTION__)));
    }

    /**
     * @throws Throwable
     */
    public function testRuleIgnoresGetterMethodsInTest(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'getClass']));
    }

    /**
     * @throws Throwable
     */
    public function testRuleIgnoresSetterMethodsInTest(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'setClass']));
    }

    /**
     * @throws Throwable
     */
    public function testRuleIgnoresCustomMethodsWhenRegexPropertyIsGiven(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'injectClass']));
    }

    /**
     * @throws Throwable
     */
    public function testRuleIgnoresGetterAndSetterMethodsInTest(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '2');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(3, ['foo', 'bar'], ['baz', 'bah']));
    }

    /**
     * @throws Throwable
     */
    public function testRuleIgnoresPrivateMethods(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '2');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'getClass', 'setClass']));
    }

    /**
     * @throws Throwable
     */
    public function testRuleIgnoresHassers(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'hasClass']));
    }

    /**
     * @throws Throwable
     */
    public function testRuleIgnoresIssers(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'isClass']));
    }

    /**
     * @throws Throwable
     */
    public function testRuleIgnoresWithers(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'withClass']));
    }

    /**
     * @throws Throwable
     */
    public function testRuleApplyToBasicClass(): void
    {
        $class = $this->getClass();
        $rule = new TooManyPublicMethods();
        $report = new Report();
        $rule->setReport($report);
        $rule->addProperty('maxmethods', '5');
        $rule->addProperty('ignorepattern', '');
        $rule->apply($class);
        $violations = $report->getRuleViolations();

        static::assertCount(1, $violations);

        static::assertSame(6, $violations[0]?->getBeginLine());
    }

    /**
     * Creates a prepared class node mock
     *
     * @param int $numberOfMethods
     * @param array<string> $publicMethods
     * @param array<string> $privateMethods
     * @return ClassNode
     * @throws Throwable
     */
    private function createClassMock($numberOfMethods, array $publicMethods = [], array $privateMethods = [])
    {
        $class = $this->getClassMock('npm', $numberOfMethods);

        $class->expects(static::any())
            ->method('getMethods')
            ->will(static::returnValue([
                ...array_map($this->createPublicMethod(...), $publicMethods),
                ...array_map($this->createPrivateMethod(...), $privateMethods),
            ]));

        return $class;
    }

    /**
     * @throws Throwable
     * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements
     */
    private function createPublicMethod(string $methodName): MethodNode
    {
        $astMethod = new ASTMethod($methodName);
        $astMethod->setModifiers(State::IS_PUBLIC);

        return new MethodNode($astMethod);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements
     */
    private function createPrivateMethod(string $methodName): MethodNode
    {
        $astMethod = new ASTMethod($methodName);

        return new MethodNode($astMethod);
    }
}
