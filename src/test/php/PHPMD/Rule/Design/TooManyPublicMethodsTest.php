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
use PHPMD\AbstractTest;
use PHPMD\Node\MethodNode;

/**
 * Test case for the too many public methods rule.
 *
 * @covers \PHPMD\Rule\Design\TooManyPublicMethods
 */
class TooManyPublicMethodsTest extends AbstractTest
{
    /**
     * @return void
     */
    public function testRuleDoesNotApplyToClassesWithLessMethodsThanThreshold()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(23));
    }

    /**
     * @return void
     */
    public function testRuleDoesNotApplyToClassesWithSameNumberOfMethodsAsThreshold()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42));
    }

    /**
     * @return void
     */
    public function testRuleAppliesToClassesWithMoreMethodsThanThreshold()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('maxmethods', '23');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42, array_fill(0, 42, __FUNCTION__)));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresGetterMethodsInTest()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'getClass')));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresSetterMethodsInTest()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'setClass')));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresCustomMethodsWhenRegexPropertyIsGiven()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'injectClass')));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresGetterAndSetterMethodsInTest()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '2');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(3, array('foo', 'bar'), array('baz', 'bah')));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresPrivateMethods()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '2');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'getClass', 'setClass')));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresHassers()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'hasClass')));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresIssers()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'isClass')));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresWithers()
    {
        $rule = new TooManyPublicMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'withClass')));
    }

    /**
     * Creates a prepared class node mock
     *
     * @param integer $numberOfMethods
     * @param array|null $publicMethods
     * @param array|null $privateMethods
     * @return \PHPMD\Node\ClassNode
     */
    private function createClassMock($numberOfMethods, array $publicMethods = array(), array $privateMethods = array())
    {
        $class = $this->getClassMock('npm', $numberOfMethods);

        $class->expects($this->any())
            ->method('getMethods')
            ->will($this->returnValue(array_merge(
                array_map(array($this, 'createPublicMethod'), $publicMethods),
                array_map(array($this, 'createPrivateMethod'), $privateMethods)
            )));

        return $class;
    }

    private function createPublicMethod($methodName)
    {
        $astMethod = new ASTMethod($methodName);
        $astMethod->setModifiers(State::IS_PUBLIC);
        return new MethodNode($astMethod);
    }

    private function createPrivateMethod($methodName)
    {
        $astMethod = new ASTMethod($methodName);
        return new MethodNode($astMethod);
    }
}
