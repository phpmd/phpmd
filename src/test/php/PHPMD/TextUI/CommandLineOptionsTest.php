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

namespace PHPMD\TextUI;

use Closure;
use InvalidArgumentException;
use PHPMD\AbstractTestCase;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Console\OutputInterface;
use PHPMD\Rule;
use ReflectionProperty;

/**
 * Test case for the {@link \PHPMD\TextUI\CommandLineOptions} class.
 *
 * @covers \PHPMD\TextUI\CommandLineOptions
 */
class CommandLineOptionsTest extends AbstractTestCase
{
    /**
     * testAssignsInputArgumentToInputProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsInputArgumentToInputProperty()
    {
        $args = ['foo.php', __FILE__, 'text', 'design'];
        $opts = new CommandLineOptions($args);

        self::assertEquals(__FILE__, $opts->getInputPath());
    }

    /**
     * @return void
     * @since 2.14.0
     */
    public function testVerbose()
    {
        $args = ['foo.php', __FILE__, 'text', 'design', '-vvv'];
        $opts = new CommandLineOptions($args);
        $renbderer = $opts->createRenderer();

        $verbosityExtractor = new ReflectionProperty('PHPMD\\Renderer\\TextRenderer', 'verbosityLevel');
        $verbosityExtractor->setAccessible(true);

        $verbosityLevel = $verbosityExtractor->getValue($renbderer);

        self::assertSame(OutputInterface::VERBOSITY_DEBUG, $verbosityLevel);
    }

    /**
     * @return void
     * @since 2.14.0
     */
    public function testColored()
    {
        $args = ['foo.php', __FILE__, 'text', 'design', '--color'];
        $opts = new CommandLineOptions($args);
        $renderer = $opts->createRenderer();

        $coloredExtractor = new ReflectionProperty('PHPMD\\Renderer\\TextRenderer', 'colored');
        $coloredExtractor->setAccessible(true);

        $colored = $coloredExtractor->getValue($renderer);

        self::assertTrue($colored);
    }

    /**
     * @return void
     * @since 2.14.0
     */
    public function testStdInDashShortCut()
    {
        $args = ['foo.php', '-', 'text', 'design'];
        $opts = new CommandLineOptions($args);

        self::assertSame('php://stdin', $opts->getInputPath());
    }

    /**
     * @return void
     * @since 2.14.0
     */
    public function testMultipleFiles()
    {
        // What happen when calling: phpmd src/*Service.php text design
        $args = ['foo.php', 'src/FooService.php', 'src/BarService.php', 'text', 'design'];
        $opts = new CommandLineOptions($args);

        self::assertSame('src/FooService.php,src/BarService.php', $opts->getInputPath());
        self::assertSame('text', $opts->getReportFormat());
        self::assertSame('design', $opts->getRuleSets());
    }

    /**
     * testAssignsFormatArgumentToReportFormatProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentToReportFormatProperty()
    {
        $args = ['foo.php', __FILE__, 'text', 'design'];
        $opts = new CommandLineOptions($args);

        self::assertEquals('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentToRuleSetProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentToRuleSetProperty()
    {
        $args = ['foo.php', __FILE__, 'text', 'design'];
        $opts = new CommandLineOptions($args);

        self::assertEquals('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenRequiredArgumentsNotSet
     *
     * @return void
     * @since 1.1.0
     */
    public function testThrowsExpectedExceptionWhenRequiredArgumentsNotSet()
    {
        self::expectException(InvalidArgumentException::class);

        $args = [__FILE__, 'text', 'design'];
        new CommandLineOptions($args);
    }

    /**
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testThrowsExpectedExceptionWhenOptionNotFound()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(
            'Unknown option --help.' . PHP_EOL .
            'If you intend to use "--help" as a value for ruleset argument, ' .
            'use the explicit argument separator:' . PHP_EOL .
            'phpmd -- text design --help'
        );

        $args = [__FILE__, 'text', 'design', '--help'];
        new CommandLineOptions($args);
    }

    /**
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testThrowsExpectedExceptionWhenOptionNotFoundInFront()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(
            'Unknown option -foobar.' . PHP_EOL .
            'If you intend to use "-foobar" as a value for input path argument, ' .
            'use the explicit argument separator:' . PHP_EOL .
            'phpmd -- -foobar text design'
        );

        $args = [__FILE__, '-foobar', 'text', 'design'];
        new CommandLineOptions($args);
    }

    /**
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testThrowsExpectedExceptionWhenOptionNotFoundUsingArgumentSeparator()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(
            'Unknown option --help.' . PHP_EOL .
            'If you intend to use "--help" as a value for input path argument, ' .
            'use the explicit argument separator:' . PHP_EOL .
            'phpmd -- --help text design'
        );

        $args = [__FILE__, '--help', '--', 'text', 'design'];
        new CommandLineOptions($args);
    }

    /**
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testThrowsExpectedExceptionWhenBooleanOptionReceiveValue()
    {
        self::expectExceptionObject(new InvalidArgumentException(
            '--color option does not accept a value',
        ));

        $args = [__FILE__, '--color=on', 'text', 'design'];
        new CommandLineOptions($args);
    }

    /**
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testOptionEqualSyntax()
    {
        $args = [__FILE__, '--exclude=*/vendor/*', '-', 'text', 'design'];
        $opts = new CommandLineOptions($args);

        self::assertSame('*/vendor/*', $opts->getIgnore());
    }

    /**
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testArgumentSeparatorEnforced()
    {
        $args = [__FILE__, '--', '--help', 'text', 'design'];
        $opts = new CommandLineOptions($args);

        self::assertSame('--help', $opts->getInputPath());
    }

    /**
     * testAssignsInputFileOptionToInputPathProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsInputFileOptionToInputPathProperty()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = ['foo.php', 'text', 'design', '--inputfile', $uri];
        $opts = new CommandLineOptions($args);

        self::assertSame('Dir1/Class1.php,Dir2/Class2.php', $opts->getInputPath());
    }

    /**
     * testAssignsFormatArgumentCorrectWhenCalledWithInputFile
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentCorrectWhenCalledWithInputFile()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = ['foo.php', 'text', 'design', '--inputfile', $uri];
        $opts = new CommandLineOptions($args);

        self::assertSame('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = ['foo.php', 'text', 'design', '--inputfile', $uri];
        $opts = new CommandLineOptions($args);

        self::assertSame('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenInputFileNotExists
     *
     * @return void
     * @since 1.1.0
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExpectedExceptionWhenInputFileNotExists()
    {
        $args = ['foo.php', 'text', 'design', '--inputfile', 'inputfail.txt'];
        new CommandLineOptions($args);
    }

    /**
     * testHasVersionReturnsFalseByDefault
     *
     * @return void
     */
    public function testHasVersionReturnsFalseByDefault()
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode'];
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->hasVersion());
    }

    /**
     * testCliOptionsAcceptsVersionArgument
     *
     * @return void
     */
    public function testCliOptionsAcceptsVersionArgument()
    {
        $args = [__FILE__, '--version'];
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->hasVersion());
    }

    /**
     * Tests if ignoreErrorsOnExit returns false by default
     *
     * @return void
     */
    public function testIgnoreErrorsOnExitReturnsFalseByDefault()
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode'];
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->ignoreErrorsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreErrorsOnExit argument
     *
     * @return void
     */
    public function testCliOptionsAcceptsIgnoreErrorsOnExitArgument()
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode', '--ignore-errors-on-exit'];
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->ignoreErrorsOnExit());
    }

    /**
     * Tests if CLI usage contains ignoreErrorsOnExit option
     *
     * @return void
     */
    public function testCliUsageContainsIgnoreErrorsOnExitOption()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertStringContainsString('--ignore-errors-on-exit:', $opts->usage());
    }

    /**
     * Tests if ignoreViolationsOnExit returns false by default
     *
     * @return void
     */
    public function testIgnoreViolationsOnExitReturnsFalseByDefault()
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode'];
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreViolationsOnExit argument
     *
     * @return void
     */
    public function testCliOptionsAcceptsIgnoreViolationsOnExitArgument()
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode', '--ignore-violations-on-exit'];
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI usage contains ignoreViolationsOnExit option
     *
     * @return void
     */
    public function testCliUsageContainsIgnoreViolationsOnExitOption()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertStringContainsString('--ignore-violations-on-exit:', $opts->usage());
    }

    /**
     * Tests if CLI usage contains the auto-discovered renderers
     *
     * @return void
     */
    public function testCliUsageContainsAutoDiscoveredRenderers()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertStringContainsString(
            'Available formats: ansi, baseline, checkstyle, github, gitlab, html, json, sarif, text, xml.',
            $opts->usage()
        );
    }

    /**
     * testCliUsageContainsStrictOption
     *
     * @return void
     */
    public function testCliUsageContainsStrictOption()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertStringContainsString('--strict:', $opts->usage());
    }

    /**
     * testCliOptionsIsStrictReturnsFalseByDefault
     *
     * @return void
     * @since 1.2.0
     */
    public function testCliOptionsIsStrictReturnsFalseByDefault()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->hasStrict());
    }

    /**
     * testCliOptionsAcceptsStrictArgument
     *
     * @return void
     * @since 1.2.0
     */
    public function testCliOptionsAcceptsStrictArgument()
    {
        $args = [__FILE__, '--strict', __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->hasStrict());

        $args = [__FILE__, '--not-strict', __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->hasStrict());
    }

    /**
     * @return void
     */
    public function testCliOptionsAcceptsMinimumpriorityArgument()
    {
        $args = [__FILE__, '--minimumpriority', 42, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertSame(42, $opts->getMinimumPriority());
    }

    /**
     * @return void
     */
    public function testCliOptionsAcceptsMaximumpriorityArgument()
    {
        $args = [__FILE__, '--maximumpriority', 42, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertSame(42, $opts->getMaximumPriority());
    }

    /**
     * @return void
     */
    public function testCliOptionGenerateBaselineFalseByDefault()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::NONE, $opts->generateBaseline());
    }

    /**
     * @return void
     */
    public function testCliOptionVerbosityNormal()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_NORMAL, $opts->getVerbosity());
    }

    /**
     * @return void
     */
    public function testCliOptionVerbosityVerbose()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '-v'];
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_VERBOSE, $opts->getVerbosity());
    }

    /**
     * @return void
     */
    public function testCliOptionVerbosityVeryVerbose()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '-vv'];
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_VERY_VERBOSE, $opts->getVerbosity());
    }

    /**
     * @return void
     */
    public function testCliOptionVerbosityDebug()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '-vvv'];
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_DEBUG, $opts->getVerbosity());
    }

    /**
     * @return void
     */
    public function testCliOptionGenerateBaselineShouldBeSet()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--generate-baseline'];
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::GENERATE, $opts->generateBaseline());
    }

    /**
     * @return void
     */
    public function testCliOptionUpdateBaselineShouldBeSet()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--update-baseline'];
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::UPDATE, $opts->generateBaseline());
    }

    /**
     * @return void
     */
    public function testCliOptionBaselineFileShouldBeNullByDefault()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);
        static::assertNull($opts->baselineFile());
    }

    /**
     * @return void
     */
    public function testCliOptionBaselineFileShouldBeWithFilename()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--baseline-file', 'foobar.txt'];
        $opts = new CommandLineOptions($args);
        static::assertSame('foobar.txt', $opts->baselineFile());
    }

    /**
     * @return void
     */
    public function testGetMinimumPriorityReturnsLowestValueByDefault()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertSame(Rule::LOWEST_PRIORITY, $opts->getMinimumPriority());
    }

    /**
     * @return void
     */
    public function testGetCoverageReportReturnsNullByDefault()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertNull($opts->getCoverageReport());
    }

    /**
     * @return void
     */
    public function testGetCoverageReportWithCliOption()
    {
        $opts = new CommandLineOptions(
            [
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--coverage',
                __METHOD__,
            ]
        );

        self::assertSame(__METHOD__, $opts->getCoverageReport());
    }

    /**
     * @return void
     */
    public function testGetCacheWithCliOption()
    {
        $opts = new CommandLineOptions(
            [
                __FILE__,
                __FILE__,
                'text',
                'codesize',
            ]
        );

        self::assertSame(ResultCacheStrategy::CONTENT, $opts->cacheStrategy());
        self::assertFalse($opts->isCacheEnabled());

        $opts = new CommandLineOptions(
            [
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--cache',
                '--cache-strategy',
                ResultCacheStrategy::TIMESTAMP,
            ]
        );

        self::assertSame(ResultCacheStrategy::TIMESTAMP, $opts->cacheStrategy());
        self::assertTrue($opts->isCacheEnabled());

        $opts = new CommandLineOptions(
            [
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--cache',
                '--cache-strategy',
                ResultCacheStrategy::CONTENT,
                '--cache-file',
                'abc',
            ]
        );

        self::assertSame(ResultCacheStrategy::CONTENT, $opts->cacheStrategy());
        self::assertSame('abc', $opts->cacheFile());
        self::assertTrue($opts->isCacheEnabled());
    }

    /**
     * @return void
     */
    public function testExcludeOption()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--ignore', 'foo/bar', '--error-file', 'abc'];
        $opts = new CommandLineOptions($args);

        self::assertSame('abc', $opts->getErrorFile());
        self::assertSame('foo/bar', $opts->getIgnore());
        self::assertSame([
            'The --ignore option is deprecated, please use --exclude instead.',
        ], $opts->getDeprecations());

        $args = [__FILE__, __FILE__, 'text', 'codesize', '--exclude', 'bar/biz'];
        $opts = new CommandLineOptions($args);

        self::assertSame('bar/biz', $opts->getIgnore());
    }

    /**
     * @param string $reportFormat
     * @param string $expectedClass
     * @return void
     * @dataProvider dataProviderCreateRenderer
     */
    public function testCreateRenderer($reportFormat, $expectedClass)
    {
        $args = [__FILE__, __FILE__, $reportFormat, 'codesize'];
        $opts = new CommandLineOptions($args);

        self::assertInstanceOf($expectedClass, $opts->createRenderer($reportFormat));
    }

    public static function dataProviderCreateRenderer(): array
    {
        return [
            ['html', 'PHPMD\\Renderer\\HtmlRenderer'],
            ['text', 'PHPMD\\Renderer\\TextRenderer'],
            ['xml', 'PHPMD\\Renderer\\XmlRenderer'],
            ['ansi', 'PHPMD\\Renderer\\AnsiRenderer'],
            ['github', 'PHPMD\\Renderer\\GitHubRenderer'],
            ['gitlab', 'PHPMD\\Renderer\\GitLabRenderer'],
            ['json', 'PHPMD\\Renderer\\JSONRenderer'],
            ['checkstyle', 'PHPMD\\Renderer\\CheckStyleRenderer'],
            ['sarif', 'PHPMD\\Renderer\\SARIFRenderer'],
            ['PHPMD_Test_Renderer_PEARRenderer', 'PHPMD_Test_Renderer_PEARRenderer'],
            ['PHPMD\\Test\\Renderer\\NamespaceRenderer', 'PHPMD\\Test\\Renderer\\NamespaceRenderer'],
            /* Test what happens when class already exists. */
            ['PHPMD\\Test\\Renderer\\NamespaceRenderer', 'PHPMD\\Test\\Renderer\\NamespaceRenderer'],
        ];
    }

    /**
     * @param string $reportFormat
     * @return void
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp (^Can\'t )
     * @dataProvider dataProviderCreateRendererThrowsException
     */
    public function testCreateRendererThrowsException($reportFormat)
    {
        $args = [__FILE__, __FILE__, $reportFormat, 'codesize'];
        $opts = new CommandLineOptions($args);
        $opts->createRenderer();
    }

    public static function dataProviderCreateRendererThrowsException(): array
    {
        return [
            [''],
            ['PHPMD\\Test\\Renderer\\NotExistsRenderer'],
        ];
    }

    /**
     * @param string $deprecatedName
     * @param string $newName
     * @param Closure $result
     * @dataProvider dataProviderDeprecatedCliOptions
     */
    public function testDeprecatedCliOptions($deprecatedName, $newName, Closure $result)
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', sprintf('--%s', $deprecatedName), '42'];
        $opts = new CommandLineOptions($args);

        self::assertSame(
            [
                sprintf(
                    'The --%s option is deprecated, please use --%s instead.',
                    $deprecatedName,
                    $newName
                ),
            ],
            $opts->getDeprecations()
        );
        $result($opts);

        $args = [__FILE__, __FILE__, 'text', 'codesize', sprintf('--%s', $newName), '42'];
        $opts = new CommandLineOptions($args);

        self::assertSame(
            [],
            $opts->getDeprecations()
        );
        $result($opts);
    }

    public static function dataProviderDeprecatedCliOptions(): array
    {
        return [
            ['extensions', 'suffixes', static function (CommandLineOptions $opts) {
                self::assertSame('42', $opts->getExtensions());
            }],
            ['ignore', 'exclude', static function (CommandLineOptions $opts) {
                self::assertSame('42', $opts->getIgnore());
            }],
        ];
    }

    /**
     * @param array $options
     * @param array $expected
     * @return void
     * @dataProvider dataProviderGetReportFiles
     */
    public function testGetReportFiles(array $options, array $expected)
    {
        $args = array_merge([__FILE__, __FILE__, 'text', 'codesize'], $options);
        $opts = new CommandLineOptions($args);

        self::assertEquals($expected, $opts->getReportFiles());
    }

    /**
     * @return void
     */
    public function testCliOptionExtraLineInExcerptShouldBeWithNumber()
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--extra-line-in-excerpt', '5'];
        $opts = new CommandLineOptions($args);
        static::assertSame(5, $opts->extraLineInExcerpt());
    }

    public static function dataProviderGetReportFiles(): array
    {
        return [
            [
                ['--reportfile-xml', __FILE__],
                ['xml' => __FILE__],
            ],
            [
                ['--reportfile-html', __FILE__],
                ['html' => __FILE__],
            ],
            [
                ['--reportfile-text', __FILE__],
                ['text' => __FILE__],
            ],
            [
                ['--reportfile-github', __FILE__],
                ['github' => __FILE__],
            ],
            [
                ['--reportfile-gitlab', __FILE__],
                ['gitlab' => __FILE__],
            ],
            [
                [
                    '--reportfile-text',
                    __FILE__,
                    '--reportfile-xml',
                    __FILE__,
                    '--reportfile-html',
                    __FILE__,
                    '--reportfile-github',
                    __FILE__,
                    '--reportfile-gitlab',
                    __FILE__,
                ],
                [
                    'text' => __FILE__,
                    'xml' => __FILE__,
                    'html' => __FILE__,
                    'github' => __FILE__,
                    'gitlab' => __FILE__,
                ],
            ],
        ];
    }
}
