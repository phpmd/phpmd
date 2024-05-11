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

/**
 * Test case for the too many public methods rule.
 *
 * @covers \PHPMD\Rule\Design\TooManyPublicMethods
 */
class TooManyPublicMethodsTest extends AbstractTestCase
{
    public function testRuleDoesNotApplyToClassesWithLessMethodsThanThreshold(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(23));
    }

    public function testRuleDoesNotApplyToClassesWithSameNumberOfMethodsAsThreshold(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42));
    }

    public function testRuleAppliesToClassesWithMoreMethodsThanThreshold(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('maxmethods', '23');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42, array_fill(0, 42, __FUNCTION__)));
    }

    public function testRuleIgnoresGetterMethodsInTest(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'getClass']));
    }

    public function testRuleIgnoresSetterMethodsInTest(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'setClass']));
    }

    public function testRuleIgnoresCustomMethodsWhenRegexPropertyIsGiven(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'injectClass']));
    }

    public function testRuleIgnoresGetterAndSetterMethodsInTest(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '2');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(3, ['foo', 'bar'], ['baz', 'bah']));
    }

    public function testRuleIgnoresPrivateMethods(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '2');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'getClass', 'setClass']));
    }

    public function testRuleIgnoresHassers(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'hasClass']));
    }

    public function testRuleIgnoresIssers(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'isClass']));
    }

    public function testRuleIgnoresWithers(): void
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, ['invoke', 'withClass']));
    }

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

        static::assertSame(6, $violations[0]->getBeginLine());
    }

    /**
     * Creates a prepared class node mock
     *
     * @param int $numberOfMethods
     * @param array|null $publicMethods
     * @param array|null $privateMethods
     * @return ClassNode
     */
    private function createClassMock($numberOfMethods, array $publicMethods = [], array $privateMethods = [])
    {
        $class = $this->getClassMock('npm', $numberOfMethods);

        $class->expects(static::any())
            ->method('getMethods')
            ->will(static::returnValue(array_merge(
                array_map([$this, 'createPublicMethod'], $publicMethods),
                array_map([$this, 'createPrivateMethod'], $privateMethods)
            )));

        return $class;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements
     */
    private function createPublicMethod($methodName)
    {
        $astMethod = new ASTMethod($methodName);
        $astMethod->setModifiers(State::IS_PUBLIC);

        return new MethodNode($astMethod);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements
     */
    private function createPrivateMethod($methodName)
    {
        $astMethod = new ASTMethod($methodName);

        return new MethodNode($astMethod);
    }
}
