<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
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
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/PMD/Rule/UnusedLocalVariable.php';

/**
 * Test case for the unused local variable rule.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 *
 * @covers PHP_PMD_Rule_UnusedLocalVariable
 * @covers PHP_PMD_Rule_AbstractLocalVariable
 * @group phpmd
 * @group phpmd::rule
 * @group unittest
 */
class PHP_PMD_Rule_UnusedLocalVariableTest extends PHP_PMD_AbstractTest
{
    /**
     * testRuleAppliesToUnusedLocalVariable
     *
     * @return void
     */
    public function testRuleAppliesToUnusedLocalVariable()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testInnerFunctionParametersDoNotHideUnusedVariables
     *
     * @return void
     */
    public function testInnerFunctionParametersDoNotHideUnusedVariables()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToLocalVariableWithSameNameAsStaticProperty
     *
     * <code>
     * class Foo
     *     protected $baz = 42;
     *     function bar() {
     *         $baz = 23;
     *         return self::$baz;
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleAppliesToLocalVariableWithSameNameAsStaticProperty()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToLocalVariableWithSameNameAsStaticArrayProperty
     *
     * <code>
     * class Foo
     *     protected $baz = array(array(1=>42));
     *     function bar() {
     *         $baz = 23;
     *         return self::$baz[0][1];
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleAppliesToLocalVariableWithSameNameAsStaticArrayProperty()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToLocalVariableUsedInCompoundVariable
     *
     * <code>
     * class Foo {
     *     protected static $bar = 42;
     *     public function baz()
     *     {
     *         $name = 'bar';
     *         return self::${$name};
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToLocalVariableUsedInCompoundVariable()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToThisVariable
     *
     * @return void
     */
    public function testRuleDoesNotApplyToThisVariable()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToStaticProperty
     *
     * @return void
     */
    public function testRuleDoesNotApplyToStaticProperty()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToStaticArrayProperty
     *
     * @return void
     */
    public function testRuleDoesNotApplyToStaticArrayProperty()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToMethodArgument
     *
     * <code>
     * class Foo {
     *     function bar() {
     *         $baz = 42;
     *         $this->foo($baz);
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToMethodArgument()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToStaticObjectProperty
     *
     * @return void
     */
    public function testRuleDoesNotApplyToStaticObjectProperty()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToDynamicProperty
     *
     * @return void
     */
    public function testRuleDoesNotApplyToDynamicProperty()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToUnusedParameters
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUnusedParameters()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToArgcSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToArgcSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToArgvSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToArgvSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToGlobalsSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToGlobalsSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToCookieSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToCookieSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToEnvSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToEnvSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToFilesSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToFilesSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToGetSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToGetSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToPostSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToPostSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToRequestSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToRequestSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToSessionSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToSessionSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToServerSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToServerSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToHttpRawPostDataSuperGlobal
     *
     * @return void
     */
    public function testRuleDoesNotApplyToHttpRawPostDataSuperGlobal()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToUnusedLocalVariableInFunction
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUnusedLocalVariableInFunction()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleDoesNotApplyToUnusedLocalVariableInMethod
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUnusedLocalVariableInMethod()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToLocalVariableUsedAsArrayIndex
     *
     * <code>
     * class Foo {
     *     public function bar() {
     *         foreach ($baz as $key) {
     *             self::$values[$key] = 42;
     *         }
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToLocalVariableUsedAsArrayIndex()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToLocalVariableUsedAsStringIndex
     *
     * <code>
     * class Foo {
     *     public function bar() {
     *         foreach ($baz as $key) {
     *             self::$string{$key} = 'a';
     *         }
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToLocalVariableUsedAsStringIndex()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToCatchStatement
     *
     * <code>
     * class Foo {
     *     public function bar() {
     *         try {
     *         } catch (Exception $e) {
     *         }
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToCatchStatement()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToCompactFunction
     *
     * <code>
     * class Foo {
     *     public function bar() {
     *         $key = 'ok';
     *         return compact('key');
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesNotApplyToCompactFunction()
    {
        $rule = new PHP_PMD_Rule_UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}
