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

use PHPMD\AbstractTest;

/**
 * Test case for the too many methods rule.
 *
 * @covers \PHPMD\Rule\Design\TooManyMethods
 */
class TooManyMethodsTest extends AbstractTest
{
    /**
     * testRuleDoesNotApplyToClassesWithLessMethodsThanThreshold
     *
     * @return void
     */
    public function testRuleDoesNotApplyToClassesWithLessMethodsThanThreshold()
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(23));
    }

    /**
     * testRuleDoesNotApplyToClassesWithSameNumberOfMethodsAsThreshold
     *
     * @return void
     */
    public function testRuleDoesNotApplyToClassesWithSameNumberOfMethodsAsThreshold()
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '42');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42));
    }

    /**
     * testRuleAppliesToClassesWithMoreMethodsThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToClassesWithMoreMethodsThanThreshold()
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('maxmethods', '23');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(42, array_fill(0, 42, __FUNCTION__)));
    }

    /**
     * testRuleIgnoresGetterMethodsInTest
     *
     * @return void
     */
    public function testRuleIgnoresGetterMethodsInTest()
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'getClass')));
    }

    /**
     * testRuleIgnoresSetterMethodsInTest
     *
     * @return void
     */
    public function testRuleIgnoresSetterMethodsInTest()
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'setClass')));
    }

   /**
     * testRuleIgnoresCustomMethodsWhenRegexPropertyIsGiven
     *
     * @return void
     */
    public function testRuleIgnoresCustomMethodsWhenRegexPropertyIsGiven()
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'injectClass')));
    }

    /**
     * testRuleIgnoresGetterAndSetterMethodsInTest
     *
     * @return void
     */
    public function testRuleIgnoresGetterAndSetterMethodsInTest()
    {
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '2');
        $rule->addProperty('ignorepattern', '(^(set|get|inject))i');
        $rule->apply($this->createClassMock(3, array('invoke', 'getClass', 'setClass')));
    }

    /**
     * @return void
     */
    public function testRuleIgnoresHassers()
    {
        $rule = new TooManyMethods();
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
        $rule = new TooManyMethods();
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
        $rule = new TooManyMethods();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maxmethods', '1');
        $rule->addProperty('ignorepattern', '(^(set|get|is|has|with))i');
        $rule->apply($this->createClassMock(2, array('invoke', 'withClass')));
    }

    /**
     * Creates a prepared class node mock
     *
     * @param integer $numberOfMethods
     * @param array$methodNames
     * @return \PHPMD\Node\ClassNode
     */
    private function createClassMock($numberOfMethods, array $methodNames = null)
    {
        $class = $this->getClassMock('nom', $numberOfMethods);

        if (is_array($methodNames)) {
            $class->expects($this->once())
                ->method('getMethodNames')
                ->will($this->returnValue($methodNames));
        }
        return $class;
    }
}
