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

namespace PHPMD;

use org\bovigo\vfs\vfsStream;

/**
 * Test case for the rule set factory class.
 *
 * @covers \PHPMD\RuleSetFactory
 */
class RuleSetFactoryTest extends AbstractTest
{
    /**
     * Used to test files/directories access for ignore code rule
     *
     * @var string
     */
    const DIR_UNDER_TESTS = 'designăôü0汉字';

    /**
     * testCreateRuleSetFileNameFindsXmlFileInBundledRuleSets
     *
     * @return void
     */
    public function testCreateRuleSetFileNameFindsXmlFileInBundledRuleSets()
    {
        $factory = new RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('codesize');

        $this->assertContains('The Code Size Ruleset', $ruleSet->getDescription());
    }

    /**
     * testCreateRuleSetFileNameFindsXmlFileInCurrentWorkingDirectory
     *
     * @return void
     */
    public function testCreateRuleSetFileNameFindsXmlFileInCurrentWorkingDirectory()
    {
        self::changeWorkingDirectory('rulesets');

        $factory = new RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('set1.xml');

        $this->assertEquals('First description...', $ruleSet->getDescription());
    }

    /**
     * testCreateRuleSetsReturnsArray
     *
     * @return void
     */
    public function testCreateRuleSetsReturnsArray()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertInternalType('array', $ruleSets);
    }

    /**
     * testCreateRuleSetsForSingleFileReturnsArrayWithOneElement
     *
     * @return void
     */
    public function testCreateRuleSetsForSingleFileReturnsArrayWithOneElement()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertEquals(1, count($ruleSets));
    }

    /**
     * testCreateRuleSetsForSingleFileReturnsOneRuleSetInstance
     *
     * @return void
     */
    public function testCreateRuleSetsForSingleFileReturnsOneRuleSetInstance()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertInstanceOf('PHPMD\\RuleSet', $ruleSets[0]);
    }

    /**
     * testCreateRuleSetsConfiguresExpectedRuleSetName
     *
     * @return void
     */
    public function testCreateRuleSetsConfiguresExpectedRuleSetName()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsConfiguresExpectedRuleSetName
     *
     * @return void
     */
    public function testCreateRuleSetsConfiguresExpectedRuleSetDescription()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        $this->assertEquals('First description...', $ruleSets[0]->getDescription());
    }

    /**
     * testCreateRuleSetsForTwoFilesReturnsArrayWithTwoElements
     *
     * @return void
     */
    public function testCreateRuleSetsForTwoFilesReturnsArrayWithTwoElements()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        $this->assertEquals(2, count($ruleSets));
    }

    /**
     * testCreateRuleSetsForTwoFilesReturnsExpectedRuleSetInstances
     *
     * @return void
     */
    public function testCreateRuleSetsForTwoFilesReturnsExpectedRuleSetInstances()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        $this->assertInstanceOf('PHPMD\\RuleSet', $ruleSets[0]);
        $this->assertInstanceOf('PHPMD\\RuleSet', $ruleSets[1]);
    }

    /**
     * testCreateRuleSetsForTwoConfiguresExpectedRuleSetNames
     *
     * @return void
     */
    public function testCreateRuleSetsForTwoConfiguresExpectedRuleSetNames()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles(
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
     */
    public function testCreateRuleSetsForTwoConfiguresExpectedRuleSetDescriptions()
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles(
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
     */
    public function testCreateRuleSetsForLocalFileNameReturnsArray()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromFiles('rulesets/set1.xml');
        $this->assertInternalType('array', $ruleSets);
    }

    /**
     * testCreateRuleSetsForSingleLocalFileNameReturnsArrayWithOneElement
     *
     * @return void
     */
    public function testCreateRuleSetsForLocalFileNameReturnsArrayWithOneElement()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromFiles('rulesets/set1.xml');
        $this->assertEquals(1, count($ruleSets));
    }

    /**
     * testCreateRuleSetsForSingleLocalFileNameConfiguresExpectedRuleSetName
     *
     * @return void
     */
    public function testCreateRuleSetsForLocalFileNameConfiguresExpectedRuleSetName()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromFiles('rulesets/set1.xml');
        $this->assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedRuleSet
     *
     * @return void
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedRuleSet()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        $this->assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedNumberOfRules
     *
     * @return void
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedNumberOfRules()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        $this->assertEquals(4, iterator_count($ruleSets[0]));
    }

    /**
     * testCreateRuleSetsForLocalFileWithRuleSetReferenceNodes
     *
     * @return void
     */
    public function testCreateRuleSetsWithReferenceContainsRuleInstances()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        $this->assertInstanceOf('PHPMD\\AbstractRule', $ruleSets[0]->getRules()->current());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedRules
     *
     * @return void
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedRules()
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/refset2.xml');

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
     */
    public function testCreateSingleRuleSetReturnsRuleSetInstance()
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('set1');

        $this->assertInstanceOf('PHPMD\\RuleSet', $ruleSet);
    }

    /**
     * Tests that the rule-set factory applies a set minimum priority filter correct.
     *
     * @return void
     */
    public function testCreateRuleSetWithSpecifiedMinimumPriorityOnlyContainsMatchingRules()
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $factory->setMinimumPriority(2);

        $ruleSet = $factory->createSingleRuleSet('set1');
        $this->assertSame(1, iterator_count($ruleSet->getRules()));
    }

    /**
     * Tests that the rule-set factory applies a set maximum priority filter correct.
     *
     * @return void
     */
    public function testCreateRuleSetWithSpecifiedMaximumPriorityOnlyContainsMatchingRules()
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $factory->setMaximumPriority(2);

        $ruleSet = $factory->createSingleRuleSet('set1');
        $this->assertSame(1, iterator_count($ruleSet->getRules()));
    }

    /**
     * Tests that the rule-set factory applies a set maximum priority filter correct.
     *
     * @return void
     */
    public function testCreateRuleSetWithSpecifiedPrioritiesOnlyContainsMatchingRules()
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $factory->setMinimumPriority(2);
        $factory->setMaximumPriority(2);

        $ruleSet = $factory->createSingleRuleSet('set1');
        $this->assertCount(0, $ruleSet->getRules());
    }

    /**
     * testCreateRuleWithExcludePattern
     *
     * @return void
     */
    public function testCreateRuleWithExcludePattern()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $excludes = $factory->getIgnorePattern('exclude-pattern');

        $expected = array(
            'some/excluded/files'
        );

        $this->assertEquals($expected, $excludes);
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesPrioritySetting
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesPrioritySetting()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertSame(4, $rule->getPriority());
    }

    /**
     * testCreateRuleWithExpectedExample
     *
     * @return void
     */
    public function testCreateRuleWithExpectedExample()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('set1');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals(array(__FUNCTION__), $rule->getExamples());
    }

    /**
     * testCreateRuleWithExpectedMultipleExamples
     *
     * @return void
     */
    public function testCreateRuleWithExpectedMultipleExamples()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('set2');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals(array(__FUNCTION__ . 'One', __FUNCTION__ . 'Two'), $rule->getExamples());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesDescriptionSetting
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesDescriptionSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertSame('description 42', $rule->getDescription());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesPropertySetting
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesPropertySetting()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertSame(42, $rule->getIntProperty('foo'));
    }

    /**
     * testFactorySupportsAlternativeSyntaxForPropertyValue
     *
     * @return void
     */
    public function testFactorySupportsAlternativeSyntaxForPropertyValue()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('alternative-property-value-syntax');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertSame(42, $rule->getIntProperty('foo'));
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();

        $examples = $rule->getExamples();
        $this->assertEquals('foreach ($foo as $bar) { echo $bar; }', $examples[0]);
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesNameSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals('Name overwritten', $rule->getName());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesMessageSetting
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesMessageSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals('Message overwritten', $rule->getMessage());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExtInfoUrlSetting
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesExtInfoUrlSetting()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertEquals('http://example.com/overwritten', $rule->getExternalInfoUrl());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceNotContainsExcludedRule
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceNotContainsExcludedRule()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset-exclude-one');

        $rules = $ruleSets[0]->getRules();
        $this->assertEquals(1, iterator_count($rules));
    }

    /**
     * testCreateRuleSetsWithRuleReferenceNotContainsExcludedRules
     *
     * @return void
     */
    public function testCreateRuleSetsWithRuleReferenceNotContainsExcludedRules()
    {
        self::changeWorkingDirectory();

        $factory  = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset-exclude-all');

        $rules = $ruleSets[0]->getRules();
        $this->assertEquals(0, iterator_count($rules));
    }

    /**
     * Tests that the factory throws the expected exception for an invalid ruleset
     * identifier.
     *
     * @return void
     * @covers \PHPMD\RuleSetNotFoundException
     */
    public function testCreateRuleSetsThrowsExceptionForInvalidIdentifier()
    {
        $factory = new RuleSetFactory();

        $this->setExpectedException(
            'PHPMD\\RuleSetNotFoundException',
            'Cannot find specified rule-set "foo-bar-ruleset-23".'
        );

        $factory->createRuleSets('foo-bar-ruleset-23');
    }

    /**
     * Tests that the factory throws an exception when the source code filename
     * for the configured rule does not exist.
     *
     * @return void
     * @covers \PHPMD\RuleClassFileNotFoundException
     */
    public function testCreateRuleSetsThrowsExceptionWhenClassFileNotInIncludePath()
    {
        $fileName = self::createFileUri('rulesets/set-class-file-not-found.xml');
        $factory  = new RuleSetFactory();

        $this->setExpectedException(
            'PHPMD\\RuleClassFileNotFoundException',
            'Cannot load source file for class: PHPMD\\Stubs\\ClassFileNotFoundRule'
        );

        $factory->createRuleSets($fileName);
    }

    /**
     * Tests that the factory throws the expected exception when a rule class
     * cannot be found.
     *
     * @return void
     * @covers \PHPMD\RuleClassNotFoundException
     */
    public function testCreateRuleSetThrowsExceptionWhenFileNotContainsClass()
    {
        $fileName = self::createFileUri('rulesets/set-class-not-found.xml');
        $factory  = new RuleSetFactory();

        $this->setExpectedException(
            'PHPMD\\RuleClassNotFoundException',
            'Cannot find rule class: PHPMD\\Stubs\\ClassNotFoundRule'
        );

        $factory->createRuleSets($fileName);
    }

    /**
     * Tests that the factory throws the expected exception when a rule class
     * cannot be found.
     *
     * @return void
     * @covers \PHPMD\RuleClassNotFoundException
     * @expectedException \RuntimeException
     */
    public function testCreateRuleSetsThrowsExpectedExceptionForInvalidXmlFile()
    {
        $fileName = self::createFileUri('rulesets/set-invalid-xml.xml');

        $factory = new RuleSetFactory();
        $factory->createRuleSets($fileName);
    }

    /**
     * testCreateRuleSetsActivatesStrictModeOnRuleSet
     *
     * @return void
     */
    public function testCreateRuleSetsActivatesStrictModeOnRuleSet()
    {
        $fileName = self::createFileUri('rulesets/set1.xml');

        $factory = new RuleSetFactory();
        $factory->setStrict();

        $ruleSets = $factory->createRuleSets($fileName);

        $this->assertAttributeEquals(true, 'strict', $ruleSets[0]);
    }

    /**
     * Tests that adding an include-path via ruleset works.
     * Also implicitly tests (by parsing the ruleset) that
     * reference-by-includepath and explicit-classfile-declaration works.
     *
     * @return void
     * @throws \Exception
     */
    public function testAddPHPIncludePath()
    {
        $includePathBefore = get_include_path();

        $rulesetFilepath = 'rulesets/ruleset-refs.xml';
        $fileName = self::createFileUri($rulesetFilepath);

        try {
            $factory = new RuleSetFactory();
            $factory->createRuleSets($fileName);

            $expectedIncludePath  = "/foo/bar/baz";
            $actualIncludePaths   = explode(PATH_SEPARATOR, get_include_path());
            $isIncludePathPresent = in_array($expectedIncludePath, $actualIncludePaths);
        } catch (\Exception $exception) {
            set_include_path($includePathBefore);
            throw $exception;
        }

        set_include_path($includePathBefore);

        $this->assertTrue(
            $isIncludePathPresent,
            "The include-path from '{$rulesetFilepath}' was not set!"
        );
    }

    /**
     * Checks if PHPMD doesn't treat directories named as code rule as files
     *
     * @return void
     * @link https://github.com/phpmd/phpmd/issues/47
     */
    public function testIfGettingRuleFilePathExcludeUnreadablePaths()
    {
        self::changeWorkingDirectory(__DIR__);
        $factory = new RuleSetFactory();
        $runtimeExceptionCount = 0;
        $ruleSetNotFoundExceptionCount = 0;

        foreach ($this->getPathsForFileAccessTest() as $path) {
            try {
                $this->assertEquals(
                    array('some/excluded/files'),
                    $factory->getIgnorePattern($path . self::DIR_UNDER_TESTS)
                );
            } catch (RuleSetNotFoundException $e) {
                $ruleSetNotFoundExceptionCount++;
            } catch (\RuntimeException $e) {
                $runtimeExceptionCount++;
            }
        }
        $this->assertEquals(0, $runtimeExceptionCount);
        $this->assertEquals(5, $ruleSetNotFoundExceptionCount);
    }

    /**
     * Invokes the <b>createRuleSets()</b> of the {@link RuleSetFactory}
     * class.
     *
     * @param string $file At least one rule configuration file name. You can
     *        also pass multiple parameters with ruleset configuration files.
     * @return \PHPMD\RuleSet[]
     */
    private function createRuleSetsFromAbsoluteFiles($file)
    {
        $files = (1 === func_num_args() ? array($file) : func_get_args());
        $files = array_map(array(__CLASS__, 'createFileUri'), $files);

        return call_user_func_array(array($this, 'createRuleSetsFromFiles'), $files);
    }

    /**
     * Invokes the <b>createRuleSets()</b> of the {@link RuleSetFactory}
     * class.
     *
     * @param string $file At least one rule configuration file name. You can
     *        also pass multiple parameters with ruleset configuration files.
     * @return \PHPMD\RuleSet[]
     */
    private function createRuleSetsFromFiles($file)
    {
        $args = func_get_args();

        $factory = new RuleSetFactory();
        return $factory->createRuleSets(join(',', $args));
    }

    /**
     * Sets up files and directories for XML rule file access test
     *
     * @return array Paths to test against
     */
    public function getPathsForFileAccessTest()
    {
        $fileContent = file_get_contents(__DIR__ . '/../../resources/files/rulesets/exclude-pattern.xml');
        $structure = array(
            'dir1' => array(
                self::DIR_UNDER_TESTS => array(), // directory - skipped
                'foo' => array(), // directory, criteria do not apply
            ),
            'dir2' => array(
                self::DIR_UNDER_TESTS => array(), // directory, wrong permissions
            ),
            'dir3' => array(
                self::DIR_UNDER_TESTS => array(), // directory, wrong owner and group
            ),
            'dirÅ' => array( // check UTF-8 characters handling
                'foo' => array(
                    self::DIR_UNDER_TESTS => $fileContent, // wrong permissions
                ),
                'bar' => array(
                    self::DIR_UNDER_TESTS => $fileContent, // OK
                ),
            ),
        );
        $root = vfsStream::setup('root', null, $structure);
        $root->getChild('dir2/' . self::DIR_UNDER_TESTS)->chmod(000);
        $root->getChild('dir3/' . self::DIR_UNDER_TESTS)->chown(vfsStream::OWNER_ROOT)->chgrp(vfsStream::GROUP_ROOT);
        $root->getChild('dirÅ/foo/' . self::DIR_UNDER_TESTS)->chmod(000);

        return array(
            $root->url(),
            $root->url() . '/dir1/',
            $root->url() . '/dir2/',
            $root->url() . '/dir3/',
            $root->url() . '/dirÅ/foo/',
            $root->url() . '/dirÅ/bar/',
        );
    }
}
