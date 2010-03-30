<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@phpmd.org>.
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
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://phpmd.org
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/PMD/RuleSetFactory.php';

/**
 * Test case for the rule set factory class.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://phpmd.org
 */
class PHP_PMD_RuleSetFactoryTest extends PHP_PMD_AbstractTest
{
    /**
     * testCreateRuleSetFileNameFindsXmlFileInBundledRuleSets
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory::_createRuleSetFileName
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetFileNameFindsXmlFileInBundledRuleSets()
    {
        $factory = new PHP_PMD_RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('codesize');

        $this->assertContains('The Code Size Ruleset', $ruleSet->getDescription());
    }

    /**
     * testCreateRuleSetFileNameFindsXmlFileInCurrentWorkingDirectory
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory::_createRuleSetFileName
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetFileNameFindsXmlFileInCurrentWorkingDirectory()
    {
        self::changeWorkingDirectory('rulesets');

        $factory = new PHP_PMD_RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('set1.xml');

        $this->assertEquals('First description...', $ruleSet->getDescription());
    }

    /**
     * testCreateRuleSetsReturnsArray
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsReturnsArray()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertType('array', $ruleSets);
    }

    /**
     * testCreateRuleSetsForSingleFileReturnsArrayWithOneElement
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForSingleFileReturnsArrayWithOneElement()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertEquals(1, count($ruleSets));
    }

    /**
     * testCreateRuleSetsForSingleFileReturnsOneRuleSetInstance
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForSingleFileReturnsOneRuleSetInstance()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertType('PHP_PMD_RuleSet', $ruleSets[0]);
    }

    /**
     * testCreateRuleSetsConfiguresExpectedRuleSetName
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsConfiguresExpectedRuleSetName()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsConfiguresExpectedRuleSetName
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsConfiguresExpectedRuleSetDescription()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertEquals('First description...', $ruleSets[0]->getDescription());
    }

    /**
     * testCreateRuleSetsForTwoFilesReturnsArrayWithTwoElements
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForTwoFilesReturnsArrayWithTwoElements()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        $this->assertEquals(2, count($ruleSets));
    }

    /**
     * testCreateRuleSetsForTwoFilesReturnsExpectedRuleSetInstances
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForTwoFilesReturnsExpectedRuleSetInstances()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        $this->assertType('PHP_PMD_RuleSet', $ruleSets[0]);
        $this->assertType('PHP_PMD_RuleSet', $ruleSets[1]);
    }

    /**
     * testCreateRuleSetsForTwoConfiguresExpectedRuleSetNames
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForTwoConfiguresExpectedRuleSetNames()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        $this->assertEquals('First Test RuleSet', $ruleSets[0]->getName());
        $this->assertEquals('Second Test RuleSet', $ruleSets[1]->getName());
    }

    /**
     * testCreateRuleSetsForTwoConfiguresExpectedRuleSetDescriptions
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForTwoConfiguresExpectedRuleSetDescriptions()
    {
        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        $this->assertSame('First description...', $ruleSets[0]->getDescription());
        $this->assertSame('Second description...', $ruleSets[1]->getDescription());
    }

    /**
     * testCreateRuleSetsForSingleLocalFileNameReturnsArray
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForLocalFileNameReturnsArray()
    {
        self::changeWorkingDirectory();
        
        $ruleSets = $this->_createRuleSetsFromFiles('rulesets/set1.xml');
        $this->assertType('array', $ruleSets);
    }

    /**
     * testCreateRuleSetsForSingleLocalFileNameReturnsArrayWithOneElement
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForLocalFileNameReturnsArrayWithOneElement()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->_createRuleSetsFromFiles('rulesets/set1.xml');
        $this->assertEquals(1, count($ruleSets));
    }

    /**
     * testCreateRuleSetsForSingleLocalFileNameConfiguresExpectedRuleSetName
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsForLocalFileNameConfiguresExpectedRuleSetName()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->_createRuleSetsFromFiles('rulesets/set1.xml');
        $this->assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedRuleSet
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedRuleSet()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        $this->assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedNumberOfRules
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedNumberOfRules()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        $this->assertEquals(4, iterator_count($ruleSets[0]));
    }

    /**
     * testCreateRuleSetsForLocalFileWithRuleSetReferenceNodes
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithReferenceContainsRuleInstances()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        $this->assertType('PHP_PMD_AbstractRule', $ruleSets[0]->getRules()->current());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedRules
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedRules()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->_createRuleSetsFromAbsoluteFiles('rulesets/refset2.xml');

        $actual   = array();
        $expected = array('RuleTwoInFirstRuleSet', 'RuleOneInSecondRuleSet');
        
        foreach ($ruleSets[0]->getRules() as $rule) {
            $actual[] = $rule->getName();
        }
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * testCreateSingleRuleSetReturnsRuleSetInstance
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateSingleRuleSetReturnsRuleSetInstance()
    {
        self::changeWorkingDirectory();

        $factory = new PHP_PMD_RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('set1');
        
        $this->assertType('PHP_PMD_RuleSet', $ruleSet);
    }

    /**
     * Tests that the rule-set factory applies a set priority filter correct.
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetWithSpecifiedPriorityOnlyContainsMatchingRules()
    {
        self::changeWorkingDirectory();

        $factory = new PHP_PMD_RuleSetFactory();
        $factory->setMinimumPriority(2);

        $ruleSet = $factory->createSingleRuleSet('set1');
        $this->assertSame(1, iterator_count($ruleSet->getRules()));
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesPrioritySetting
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesPrioritySetting()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertSame(-42, $rule->getPriority());
    }

    /**
     * testCreateRuleWithExpectedExample
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleWithExpectedExample()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('set1');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals(array(__FUNCTION__), $rule->getExamples());
    }

    /**
     * testCreateRuleWithExpectedMultipleExamples
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleWithExpectedMultipleExamples()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('set2');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals(array(__FUNCTION__ . 'One', __FUNCTION__ . 'Two'), $rule->getExamples());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesDescriptionSetting
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesDescriptionSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertSame('description 42', $rule->getDescription());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesPropertySetting
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesPropertySetting()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertSame(42, $rule->getIntProperty('foo'));
    }

    /**
     * testFactorySupportsAlternativeSyntaxForPropertyValue
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testFactorySupportsAlternativeSyntaxForPropertyValue()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('alternative-property-value-syntax');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertSame(42, $rule->getIntProperty('foo'));
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();

        $examples = $rule->getExamples();
        $this->assertEquals('foreach ($foo as $bar) { echo $bar; }', $examples[0]);
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesNameSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals('Name overwritten', $rule->getName());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesMessageSetting
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesMessageSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals('Message overwritten', $rule->getMessage());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExtInfoUrlSetting
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesExtInfoUrlSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals('http://example.com/overwritten', $rule->getExternalInfoUrl());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceNotContainsExcludedRule
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceNotContainsExcludedRule()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset-exclude-one');

        $rules = $ruleSets[0]->getRules();
        $this->assertEquals(1, iterator_count($rules));
    }

    /**
     * testCreateRuleSetsWithRuleReferenceNotContainsExcludedRules
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsWithRuleReferenceNotContainsExcludedRules()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset-exclude-all');

        $rules = $ruleSets[0]->getRules();
        $this->assertEquals(0, iterator_count($rules));
    }

    /**
     * Tests that the factory throws the expected exception for an invalid ruleset
     * identifier.
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @covers PHP_PMD_RuleSetNotFoundException
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsThrowsExceptionForInvalidIdentifier()
    {
        $factory = new PHP_PMD_RuleSetFactory();

        $this->setExpectedException(
            'PHP_PMD_RuleSetNotFoundException',
            'Cannot find specified rule-set "foo-bar-ruleset-23".'
        );

        $factory->createRuleSets('foo-bar-ruleset-23');
    }

    /**
     * Tests that the factory throws an exception when the source code filename
     * for the configured rule does not exist.
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @covers PHP_PMD_RuleClassFileNotFoundException
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetsThrowsExceptionWhenClassFileNotInIncludePath()
    {
        $fileName = self::createFileUri('rulesets/set-class-file-not-found.xml');
        $factory  = new PHP_PMD_RuleSetFactory();

        $this->setExpectedException(
            'PHP_PMD_RuleClassFileNotFoundException',
            'Cannot load source file for class: rules_ClassFileNotFoundRule'
        );

        $factory->createRuleSets($fileName);
    }

    /**
     * Tests that the factory throws the expected exception when a rule class
     * cannot be found.
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @covers PHP_PMD_RuleClassNotFoundException
     * @group phpmd
     * @group unittest
     */
    public function testCreateRuleSetThrowsExceptionWhenFileNotContainsClass()
    {
        $fileName = self::createFileUri('rulesets/set-class-not-found.xml');
        $factory  = new PHP_PMD_RuleSetFactory();

        $this->setExpectedException(
            'PHP_PMD_RuleClassNotFoundException',
            'Cannot find rule class: rules_ClassNotFoundRule'
        );

        $factory->createRuleSets($fileName);
    }

    /**
     * Tests that the factory throws the expected exception when a rule class
     * cannot be found.
     *
     * @return void
     * @covers PHP_PMD_RuleSetFactory
     * @covers PHP_PMD_RuleClassNotFoundException
     * @group phpmd
     * @group unittest
     * @expectedException RuntimeException
     */
    public function testCreateRuleSetsThrowsExpectedExceptionForInvalidXmlFile()
    {
        $fileName = self::createFileUri('rulesets/set-invalid-xml.xml');

        $factory = new PHP_PMD_RuleSetFactory();
        $factory->createRuleSets($fileName);
    }

    /**
     * Invokes the <b>createRuleSets()</b> of the {@link PHP_PMD_RuleSetFactory}
     * class.
     *
     * @param string $file At least one rule configuration file name. You can
     *        also pass multiple parameters with ruleset configuration files.
     *
     * @return array(PHP_PMD_RuleSet)
     */
    private function _createRuleSetsFromAbsoluteFiles($file)
    {
        $files = func_get_args();
        $files = array_map(array(__CLASS__, 'createFileUri'), $files);

        return call_user_func_array(array($this, '_createRuleSetsFromFiles'), $files);
    }

    /**
     * Invokes the <b>createRuleSets()</b> of the {@link PHP_PMD_RuleSetFactory}
     * class.
     *
     * @param string $file At least one rule configuration file name. You can
     *        also pass multiple parameters with ruleset configuration files.
     *
     * @return array(PHP_PMD_RuleSet)
     */
    private function _createRuleSetsFromFiles($file)
    {
        $args = func_get_args();

        $factory = new PHP_PMD_RuleSetFactory();
        return $factory->createRuleSets(join(',', $args));
    }
}
