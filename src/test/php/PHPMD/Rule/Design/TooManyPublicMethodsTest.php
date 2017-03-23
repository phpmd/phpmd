<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2017, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *_Design
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule\Design;

use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\State;
use PHPMD\AbstractTest;
use PHPMD\Node\MethodNode;

/**
 * Test case for the too many public methods rule.
 *_Design
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Rule\Design\TooManyPublicMethods
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::design
 * @group unittest
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
