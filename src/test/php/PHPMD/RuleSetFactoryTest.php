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

use Exception;
use org\bovigo\vfs\vfsStream;
use PHPMD\Exception\RuleClassFileNotFoundException;
use PHPMD\Exception\RuleClassNotFoundException;
use PHPMD\Exception\RuleSetNotFoundException;
use RuntimeException;
use Throwable;

/**
 * Test case for the rule set factory class.
 *
 * @covers \PHPMD\RuleSetFactory
 */
class RuleSetFactoryTest extends AbstractTestCase
{
    /**
     * Used to test files/directories access for ignore code rule
     *
     * @var string
     */
    private const DIR_UNDER_TESTS = 'designăôü0汉字';

    /**
     * testCreateRuleSetFileNameFindsXmlFileInBundledRuleSets
     * @throws Throwable
     */
    public function testCreateRuleSetFileNameFindsXmlFileInBundledRuleSets(): void
    {
        $factory = new RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('codesize');

        static::assertStringContainsString('The Code Size Ruleset', $ruleSet->getDescription());
    }

    /**
     * testCreateRuleSetFileNameFindsXmlFileInCurrentWorkingDirectory
     * @throws Throwable
     */
    public function testCreateRuleSetFileNameFindsXmlFileInCurrentWorkingDirectory(): void
    {
        self::changeWorkingDirectory('rulesets');

        $factory = new RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('set1.xml');

        static::assertEquals('First description...', $ruleSet->getDescription());
    }

    /**
     * testCreateRuleSetsReturnsArray
     * @throws Throwable
     */
    public function testCreateRuleSetsReturnsArray(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        static::assertIsArray($ruleSets);
    }

    /**
     * testCreateRuleSetsForSingleFileReturnsArrayWithOneElement
     * @throws Throwable
     */
    public function testCreateRuleSetsForSingleFileReturnsArrayWithOneElement(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        static::assertCount(1, $ruleSets);
    }

    /**
     * testCreateRuleSetsForSingleFileReturnsOneRuleSetInstance
     * @throws Throwable
     */
    public function testCreateRuleSetsForSingleFileReturnsOneRuleSetInstance(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        static::assertInstanceOf(RuleSet::class, $ruleSets[0]);
    }

    /**
     * testCreateRuleSetsConfiguresExpectedRuleSetName
     * @throws Throwable
     */
    public function testCreateRuleSetsConfiguresExpectedRuleSetName(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        static::assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsConfiguresExpectedRuleSetName
     * @throws Throwable
     */
    public function testCreateRuleSetsConfiguresExpectedRuleSetDescription(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/set1.xml');
        static::assertEquals('First description...', $ruleSets[0]->getDescription());
    }

    /**
     * testCreateRuleSetsForTwoFilesReturnsArrayWithTwoElements
     * @throws Throwable
     */
    public function testCreateRuleSetsForTwoFilesReturnsArrayWithTwoElements(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        static::assertCount(2, $ruleSets);
    }

    /**
     * testCreateRuleSetsForTwoFilesReturnsExpectedRuleSetInstances
     * @throws Throwable
     */
    public function testCreateRuleSetsForTwoFilesReturnsExpectedRuleSetInstances(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        static::assertInstanceOf(RuleSet::class, $ruleSets[0]);
        static::assertInstanceOf(RuleSet::class, $ruleSets[1]);
    }

    /**
     * testCreateRuleSetsForTwoConfiguresExpectedRuleSetNames
     * @throws Throwable
     */
    public function testCreateRuleSetsForTwoConfiguresExpectedRuleSetNames(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        static::assertEquals('First Test RuleSet', $ruleSets[0]->getName());
        static::assertEquals('Second Test RuleSet', $ruleSets[1]->getName());
    }

    /**
     * testCreateRuleSetsForTwoConfiguresExpectedRuleSetDescriptions
     * @throws Throwable
     */
    public function testCreateRuleSetsForTwoConfiguresExpectedRuleSetDescriptions(): void
    {
        $ruleSets = $this->createRuleSetsFromAbsoluteFiles(
            'rulesets/set1.xml',
            'rulesets/set2.xml'
        );
        static::assertSame('First description...', $ruleSets[0]->getDescription());
        static::assertSame('Second description...', $ruleSets[1]->getDescription());
    }

    /**
     * testCreateRuleSetsForSingleLocalFileNameReturnsArray
     * @throws Throwable
     */
    public function testCreateRuleSetsForLocalFileNameReturnsArray(): void
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromFiles('rulesets/set1.xml');
        static::assertIsArray($ruleSets);
    }

    /**
     * testCreateRuleSetsForSingleLocalFileNameReturnsArrayWithOneElement
     * @throws Throwable
     */
    public function testCreateRuleSetsForLocalFileNameReturnsArrayWithOneElement(): void
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromFiles('rulesets/set1.xml');
        static::assertCount(1, $ruleSets);
    }

    /**
     * testCreateRuleSetsForSingleLocalFileNameConfiguresExpectedRuleSetName
     * @throws Throwable
     */
    public function testCreateRuleSetsForLocalFileNameConfiguresExpectedRuleSetName(): void
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromFiles('rulesets/set1.xml');
        static::assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedRuleSet
     * @throws Throwable
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedRuleSet(): void
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        static::assertEquals('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedNumberOfRules
     * @throws Throwable
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedNumberOfRules(): void
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        static::assertEquals(4, iterator_count($ruleSets[0]));
    }

    /**
     * testCreateRuleSetsForLocalFileWithRuleSetReferenceNodes
     * @throws Throwable
     */
    public function testCreateRuleSetsWithReferenceContainsRuleInstances(): void
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/refset1.xml');
        static::assertInstanceOf(AbstractRule::class, $ruleSets[0]->getRules()->current());
    }

    /**
     * testCreateRuleSetsWithReferenceContainsExpectedRules
     * @throws Throwable
     */
    public function testCreateRuleSetsWithReferenceContainsExpectedRules(): void
    {
        self::changeWorkingDirectory();

        $ruleSets = $this->createRuleSetsFromAbsoluteFiles('rulesets/refset2.xml');

        $actual = [];
        $expected = ['RuleTwoInFirstRuleSet', 'RuleOneInSecondRuleSet'];

        foreach ($ruleSets[0]->getRules() as $rule) {
            $actual[] = $rule->getName();
        }

        static::assertEquals($expected, $actual);
    }

    /**
     * testCreateSingleRuleSetReturnsRuleSetInstance
     * @throws Throwable
     */
    public function testCreateSingleRuleSetReturnsRuleSetInstance(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet('set1');

        static::assertInstanceOf(RuleSet::class, $ruleSet);
    }

    /**
     * Tests that the rule-set factory applies a set minimum priority filter correct.
     * @throws Throwable
     */
    public function testCreateRuleSetWithSpecifiedMinimumPriorityOnlyContainsMatchingRules(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $factory->setMinimumPriority(2);

        $ruleSet = $factory->createSingleRuleSet('set1');
        static::assertSame(1, iterator_count($ruleSet->getRules()));
    }

    /**
     * Tests that the rule-set factory applies a set maximum priority filter correct.
     * @throws Throwable
     */
    public function testCreateRuleSetWithSpecifiedMaximumPriorityOnlyContainsMatchingRules(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $factory->setMaximumPriority(2);

        $ruleSet = $factory->createSingleRuleSet('set1');
        static::assertSame(1, iterator_count($ruleSet->getRules()));
    }

    /**
     * Tests that the rule-set factory applies a set maximum priority filter correct.
     * @throws Throwable
     */
    public function testCreateRuleSetWithSpecifiedPrioritiesOnlyContainsMatchingRules(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $factory->setMinimumPriority(2);
        $factory->setMaximumPriority(2);

        $ruleSet = $factory->createSingleRuleSet('set1');
        static::assertCount(0, $ruleSet->getRules());
    }

    /**
     * testCreateRuleWithExcludePattern
     * @throws Throwable
     */
    public function testCreateRuleWithExcludePattern(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $excludes = $factory->getIgnorePattern('exclude-pattern');

        $expected = [
            '*sourceExcluded/*.php',
            '*sourceExcluded\*.php',
        ];

        static::assertEquals($expected, $excludes);
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesPrioritySetting
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesPrioritySetting(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertSame(4, $rule->getPriority());
    }

    /**
     * testCreateRuleWithExpectedExample
     * @throws Throwable
     */
    public function testCreateRuleWithExpectedExample(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('set1');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertEquals([__FUNCTION__], $rule->getExamples());
    }

    /**
     * testCreateRuleWithExpectedMultipleExamples
     * @throws Throwable
     */
    public function testCreateRuleWithExpectedMultipleExamples(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('set2');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertEquals([__FUNCTION__ . 'One', __FUNCTION__ . 'Two'], $rule->getExamples());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesDescriptionSetting
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesDescriptionSetting(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertSame('description 42', $rule->getDescription());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesPropertySetting
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesPropertySetting(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertSame(42, $rule->getIntProperty('foo'));
    }

    /**
     * testFactorySupportsAlternativeSyntaxForPropertyValue
     * @throws Throwable
     */
    public function testFactorySupportsAlternativeSyntaxForPropertyValue(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('alternative-property-value-syntax');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertSame(42, $rule->getIntProperty('foo'));
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $rule = $ruleSets[0]->getRules()->current();

        $examples = $rule->getExamples();
        static::assertEquals('foreach ($foo as $bar) { echo $bar; }', $examples[0]);
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExamplesSetting
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesNameSetting(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertEquals('Name overwritten', $rule->getName());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesMessageSetting
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesMessageSetting(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertEquals('Message overwritten', $rule->getMessage());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceThatOverwritesExtInfoUrlSetting
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceThatOverwritesExtInfoUrlSetting(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset4');

        $rule = $ruleSets[0]->getRules()->current();
        static::assertEquals('http://example.com/overwritten', $rule->getExternalInfoUrl());
    }

    /**
     * testCreateRuleSetsWithRuleReferenceNotContainsExcludedRule
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceNotContainsExcludedRule(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset-exclude-one');

        $rules = $ruleSets[0]->getRules();
        static::assertEquals(1, iterator_count($rules));
    }

    /**
     * testCreateRuleSetsWithRuleReferenceNotContainsExcludedRules
     * @throws Throwable
     */
    public function testCreateRuleSetsWithRuleReferenceNotContainsExcludedRules(): void
    {
        self::changeWorkingDirectory();

        $factory = new RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset-exclude-all');

        $rules = $ruleSets[0]->getRules();
        static::assertEquals(0, iterator_count($rules));
    }

    /**
     * Tests that the factory throws the expected exception for an invalid ruleset
     * identifier.
     *
     * @throws Throwable
     * @covers \PHPMD\Exception\RuleSetNotFoundException
     */
    public function testCreateRuleSetsThrowsExceptionForInvalidIdentifier(): void
    {
        self::expectExceptionObject(new RuleSetNotFoundException('foo-bar-ruleset-23'));

        $factory = new RuleSetFactory();

        $factory->createRuleSets('foo-bar-ruleset-23');
    }

    /**
     * Tests that the factory throws an exception when the source code filename
     * for the configured rule does not exist.
     *
     * @throws Throwable
     * @covers \PHPMD\Exception\RuleClassFileNotFoundException
     */
    public function testCreateRuleSetsThrowsExceptionWhenClassFileNotInIncludePath(): void
    {
        self::expectExceptionObject(new RuleClassFileNotFoundException(
            'PHPMD\\Stubs\\ClassFileNotFoundRule',
        ));

        $fileName = self::createFileUri('rulesets/set-class-file-not-found.xml');
        $factory = new RuleSetFactory();

        $factory->createRuleSets($fileName);
    }

    /**
     * Tests that the factory throws the expected exception when a rule class
     * cannot be found.
     *
     * @throws Throwable
     * @covers \PHPMD\Exception\RuleClassNotFoundException
     */
    public function testCreateRuleSetThrowsExceptionWhenFileNotContainsClass(): void
    {
        self::expectExceptionObject(new RuleClassNotFoundException(
            'PHPMD\\Stubs\\ClassNotFoundRule',
        ));
        $fileName = self::createFileUri('rulesets/set-class-not-found.xml');
        $factory = new RuleSetFactory();

        $factory->createRuleSets($fileName);
    }

    /**
     * Tests that the factory throws the expected exception when a rule class
     * cannot be found.
     *
     * @throws Throwable
     * @covers \PHPMD\Exception\RuleClassNotFoundException
     */
    public function testCreateRuleSetsThrowsExpectedExceptionForInvalidXmlFile(): void
    {
        self::expectException(RuntimeException::class);

        $fileName = self::createFileUri('rulesets/set-invalid-xml.xml');

        $factory = new RuleSetFactory();
        $factory->createRuleSets($fileName);
    }

    /**
     * testCreateRuleSetsActivatesStrictModeOnRuleSet
     * @throws Throwable
     */
    public function testCreateRuleSetsActivatesStrictModeOnRuleSet(): void
    {
        $fileName = self::createFileUri('rulesets/set1.xml');

        $factory = new RuleSetFactory();
        $factory->setStrict();

        $ruleSets = $factory->createRuleSets($fileName);

        static::assertTrue($ruleSets[0]->isStrict());
    }

    /**
     * Tests that adding an include-path via ruleset works.
     * Also implicitly tests (by parsing the ruleset) that
     * reference-by-includepath and explicit-classfile-declaration works.
     *
     * @throws Throwable
     */
    public function testAddPHPIncludePath(): void
    {
        $includePathBefore = get_include_path();

        $rulesetFilepath = 'rulesets/ruleset-refs.xml';
        $fileName = self::createFileUri($rulesetFilepath);

        try {
            $factory = new RuleSetFactory();
            $factory->createRuleSets($fileName);

            $expectedIncludePath = '/foo/bar/baz';
            $actualIncludePaths = explode(PATH_SEPARATOR, get_include_path());
            $isIncludePathPresent = in_array($expectedIncludePath, $actualIncludePaths, true);
        } catch (Exception $exception) {
            set_include_path($includePathBefore);

            throw $exception;
        }

        set_include_path($includePathBefore);

        static::assertTrue(
            $isIncludePathPresent,
            "The include-path from '{$rulesetFilepath}' was not set!"
        );
    }

    /**
     * Checks if PHPMD doesn't treat directories named as code rule as files
     *
     * @throws Throwable
     * @link https://github.com/phpmd/phpmd/issues/47
     */
    public function testIfGettingRuleFilePathExcludeUnreadablePaths(): void
    {
        self::changeWorkingDirectory(__DIR__);
        $factory = new RuleSetFactory();
        $runtimeExceptionCount = 0;
        $ruleSetNotFoundExceptionCount = 0;

        foreach ($this->getPathsForFileAccessTest() as $path) {
            try {
                static::assertEquals(
                    [
                        '*sourceExcluded/*.php',
                        '*sourceExcluded\*.php',
                    ],
                    $factory->getIgnorePattern($path . self::DIR_UNDER_TESTS)
                );
            } catch (RuleSetNotFoundException) {
                $ruleSetNotFoundExceptionCount++;
            } catch (RuntimeException) {
                $runtimeExceptionCount++;
            }
        }
        static::assertEquals(0, $runtimeExceptionCount);
        static::assertEquals(5, $ruleSetNotFoundExceptionCount);
    }

    /**
     * Checks the ruleset XML files provided with PHPMD all provide externalInfoUrls
     *
     * @param string $file The path to the ruleset xml to test
     * @throws Throwable
     * @dataProvider getDefaultRuleSets
     */
    public function testDefaultRuleSetsProvideExternalInfoUrls($file): void
    {
        $ruleSets = $this->createRuleSetsFromFiles($file);
        $ruleSet = $ruleSets[0];

        foreach ($ruleSet->getRules() as $rule) {
            $message = sprintf(
                '%s in rule set %s should provide an externalInfoUrl',
                $rule->getName(),
                $ruleSet->getName()
            );

            static::assertNotEmpty($rule->getExternalInfoUrl(), $message);
        }
    }

    /**
     * Provides an array of the file paths to rule sets provided with PHPMD
     *
     * @return array<array<string>>
     */
    public static function getDefaultRuleSets(): array
    {
        return static::getValuesAsArrays(glob(__DIR__ . '/../../../main/resources/rulesets/*.xml') ?: []);
    }

    /**
     * Invokes the <b>createRuleSets()</b> of the {@link RuleSetFactory}
     * class.
     *
     * @param string $files At least one rule configuration file name. You can
     *        also pass multiple parameters with ruleset configuration files.
     * @return \PHPMD\RuleSet[]
     */
    private function createRuleSetsFromAbsoluteFiles(string ...$files)
    {
        $files = array_map(static::createFileUri(...), $files);

        return $this->createRuleSetsFromFiles(...$files);
    }

    /**
     * Invokes the <b>createRuleSets()</b> of the {@link RuleSetFactory}
     * class.
     *
     * @param string $file At least one rule configuration file name. You can
     *        also pass multiple parameters with ruleset configuration files.
     * @return \PHPMD\RuleSet[]
     * @throws Throwable
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    private function createRuleSetsFromFiles($file)
    {
        $args = func_get_args();

        $factory = new RuleSetFactory();

        return $factory->createRuleSets(implode(',', $args));
    }

    /**
     * Sets up files and directories for XML rule file access test
     *
     * @return list<string> Paths to test against
     */
    public function getPathsForFileAccessTest()
    {
        $fileContent = file_get_contents(__DIR__ . '/../../resources/files/rulesets/exclude-pattern.xml');
        $structure = [
            'dir1' => [
                self::DIR_UNDER_TESTS => [], // directory - skipped
                'foo' => [], // directory, criteria do not apply
            ],
            'dir2' => [
                self::DIR_UNDER_TESTS => [], // directory, wrong permissions
            ],
            'dir3' => [
                self::DIR_UNDER_TESTS => [], // directory, wrong owner and group
            ],
            'dirÅ' => [ // check UTF-8 characters handling
                'foo' => [
                    self::DIR_UNDER_TESTS => $fileContent, // wrong permissions
                ],
                'bar' => [
                    self::DIR_UNDER_TESTS => $fileContent, // OK
                ],
            ],
        ];
        $root = vfsStream::setup('root', null, $structure);
        $root->getChild('dir2/' . self::DIR_UNDER_TESTS)->chmod(0o000);
        $root->getChild('dir3/' . self::DIR_UNDER_TESTS)->chown(vfsStream::OWNER_ROOT)->chgrp(vfsStream::GROUP_ROOT);
        $root->getChild('dirÅ/foo/' . self::DIR_UNDER_TESTS)->chmod(0o000);

        return [
            $root->url(),
            $root->url() . '/dir1/',
            $root->url() . '/dir2/',
            $root->url() . '/dir3/',
            $root->url() . '/dirÅ/foo/',
            $root->url() . '/dirÅ/bar/',
        ];
    }
}
