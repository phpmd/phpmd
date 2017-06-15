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

namespace PHPMD\Rule;

use PHPMD\AbstractTest;

/**
 * Test case for the unused local variable rule.
 *
 * @covers \PHPMD\Rule\UnusedLocalVariable
 * @covers \PHPMD\Rule\AbstractLocalVariable
 */
class UnusedLocalVariableTest extends AbstractTest
{
    /**
     * testRuleAppliesToUnusedLocalVariable
     *
     * @return void
     */
    public function testRuleAppliesToUnusedLocalVariable()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesApplyToCompoundVariableInString
     *
     * <code>
     * class Foo
     *     function bar() {
     *         $baz = 23;
     *         return "${baz}_wibble";
     *     }
     * }
     * </code>
     *
     * @return void
     */
    public function testRuleDoesApplyToCompoundVariableInString()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(0));
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToUnusedForeachKeyWhenNotIgnored
     *
     * @return void
     */
    public function testRuleAppliesToUnusedForeachKeyWhenNotIgnored()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToUnusedForeachValueWhenNotIgnored
     *
     * @return void
     */
    public function testRuleAppliesToUnusedForeachValueWhenNotIgnored()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToUnusedForeachKeyWhenIgnored
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUnusedForeachKeyWhenIgnored()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'true');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToUnusedForeachKeyWhenWhitelisted
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUnusedForeachKeyWhenWhitelisted()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->addProperty('exceptions', '_');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotAppliesToWhitelistedUnusedLocaleVariable
     *
     * @return void
     */
    public function testRuleDoesNotAppliesToWhitelistedUnusedLocaleVariable()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->addProperty('exceptions', '_');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleStillAppliesWhenSomeUnusedLocaleAreWhitelisted
     *
     * @return void
     */
    public function testRuleStillAppliesWhenSomeUnusedLocaleAreWhitelisted()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->addProperty('exceptions', '_');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToUnusedForeachValueWhenIgnored
     *
     * @return void
     */
    public function testRuleDoesNotApplyToUnusedForeachValueWhenIgnored()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'true');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
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
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.0
     */
    public function testCompactFunctionRuleWorksCaseInsensitive()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleDoesNotApplyToNamespacedCompactFunction
     *
     * <code>
     * namespace Baz;
     *
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
    public function testRuleDoesNotApplyToNamespacedCompactFunction()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * @test
     * @return void
     * @since 2.0.1
     */
    public function testNamespacedCompactFunctionRuleWorksCaseInsensitive()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToPredefinedVariables.php
     *
     * @return void
     */
    public function testRuleNotAppliesToPredefinedVariables()
    {
        $rule = new UnusedLocalVariable();
        $rule->addProperty('allow-unused-foreach-variables', 'false');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }
}
