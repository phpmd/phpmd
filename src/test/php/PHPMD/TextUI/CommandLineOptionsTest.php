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
use PHPMD\Renderer\AnsiRenderer;
use PHPMD\Renderer\CheckStyleRenderer;
use PHPMD\Renderer\GitHubRenderer;
use PHPMD\Renderer\GitLabRenderer;
use PHPMD\Renderer\HTMLRenderer;
use PHPMD\Renderer\JSONRenderer;
use PHPMD\Renderer\RendererInterface;
use PHPMD\Renderer\SARIFRenderer;
use PHPMD\Renderer\TextRenderer;
use PHPMD\Renderer\XMLRenderer;
use PHPMD\Rule;
use ReflectionProperty;
use Throwable;

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
     * @throws Throwable
     * @since 1.1.0
     */
    public function testAssignsInputArgumentToInputProperty(): void
    {
        $args = ['foo.php', __FILE__, 'text', 'design'];
        $opts = new CommandLineOptions($args);

        static::assertEquals(__FILE__, $opts->getInputPath());
    }

    /**
     * @throws Throwable
     * @since 2.14.0
     */
    public function testVerbose(): void
    {
        $args = ['foo.php', __FILE__, 'text', 'design', '-vvv'];
        $opts = new CommandLineOptions($args);
        $renderer = $opts->createRenderer();

        $verbosityExtractor = new ReflectionProperty(TextRenderer::class, 'verbosityLevel');
        $verbosityExtractor->setAccessible(true);

        $verbosityLevel = $verbosityExtractor->getValue($renderer);

        static::assertSame(OutputInterface::VERBOSITY_DEBUG, $verbosityLevel);
    }

    /**
     * @throws Throwable
     * @since 2.14.0
     */
    public function testColored(): void
    {
        $args = ['foo.php', __FILE__, 'text', 'design', '--color'];
        $opts = new CommandLineOptions($args);
        $renderer = $opts->createRenderer();

        $coloredExtractor = new ReflectionProperty(TextRenderer::class, 'colored');
        $coloredExtractor->setAccessible(true);

        $colored = $coloredExtractor->getValue($renderer);

        static::assertTrue($colored);
    }

    /**
     * @throws Throwable
     * @since 2.14.0
     */
    public function testStdInDashShortCut(): void
    {
        $args = ['foo.php', '-', 'text', 'design'];
        $opts = new CommandLineOptions($args);

        static::assertSame('php://stdin', $opts->getInputPath());
    }

    /**
     * @throws Throwable
     * @since 2.14.0
     */
    public function testMultipleFiles(): void
    {
        // What happen when calling: phpmd src/*Service.php text design
        $args = ['foo.php', 'src/FooService.php', 'src/BarService.php', 'text', 'design'];
        $opts = new CommandLineOptions($args);

        static::assertSame('src/FooService.php,src/BarService.php', $opts->getInputPath());
        static::assertSame('text', $opts->getReportFormat());
        static::assertSame('design', $opts->getRuleSets());
    }

    /**
     * testAssignsFormatArgumentToReportFormatProperty
     *
     * @throws Throwable
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentToReportFormatProperty(): void
    {
        $args = ['foo.php', __FILE__, 'text', 'design'];
        $opts = new CommandLineOptions($args);

        static::assertEquals('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentToRuleSetProperty
     *
     * @throws Throwable
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentToRuleSetProperty(): void
    {
        $args = ['foo.php', __FILE__, 'text', 'design'];
        $opts = new CommandLineOptions($args);

        static::assertEquals('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenRequiredArgumentsNotSet
     *
     * @throws Throwable
     * @since 1.1.0
     */
    public function testThrowsExpectedExceptionWhenRequiredArgumentsNotSet(): void
    {
        self::expectException(InvalidArgumentException::class);

        $args = [__FILE__, 'text', 'design'];
        new CommandLineOptions($args);
    }

    /**
     * @throws Throwable
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testThrowsExpectedExceptionWhenOptionNotFound(): void
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
     * @throws Throwable
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testThrowsExpectedExceptionWhenOptionNotFoundInFront(): void
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
     * @throws Throwable
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testThrowsExpectedExceptionWhenOptionNotFoundUsingArgumentSeparator(): void
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
     * @throws Throwable
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testThrowsExpectedExceptionWhenBooleanOptionReceiveValue(): void
    {
        self::expectExceptionObject(new InvalidArgumentException(
            '--color option does not accept a value',
        ));

        $args = [__FILE__, '--color=on', 'text', 'design'];
        new CommandLineOptions($args);
    }

    /**
     * @throws Throwable
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testOptionEqualSyntax(): void
    {
        $args = [__FILE__, '--exclude=*/vendor/*', '-', 'text', 'design'];
        $opts = new CommandLineOptions($args);

        static::assertSame('*/vendor/*', $opts->getIgnore());
    }

    /**
     * @throws Throwable
     * @covers \PHPMD\Utility\ArgumentsValidator
     */
    public function testArgumentSeparatorEnforced(): void
    {
        $args = [__FILE__, '--', '--help', 'text', 'design'];
        $opts = new CommandLineOptions($args);

        static::assertSame('--help', $opts->getInputPath());
    }

    /**
     * testAssignsInputFileOptionToInputPathProperty
     *
     * @throws Throwable
     * @since 1.1.0
     */
    public function testAssignsInputFileOptionToInputPathProperty(): void
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = ['foo.php', 'text', 'design', '--inputfile', $uri];
        $opts = new CommandLineOptions($args);

        static::assertSame('Dir1/Class1.php,Dir2/Class2.php', $opts->getInputPath());
    }

    /**
     * testAssignsFormatArgumentCorrectWhenCalledWithInputFile
     *
     * @throws Throwable
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentCorrectWhenCalledWithInputFile(): void
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = ['foo.php', 'text', 'design', '--inputfile', $uri];
        $opts = new CommandLineOptions($args);

        static::assertSame('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile
     *
     * @throws Throwable
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile(): void
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = ['foo.php', 'text', 'design', '--inputfile', $uri];
        $opts = new CommandLineOptions($args);

        static::assertSame('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenInputFileNotExists
     *
     * @throws Throwable
     * @since 1.1.0
     */
    public function testThrowsExpectedExceptionWhenInputFileNotExists(): void
    {
        self::expectExceptionObject(new InvalidArgumentException(
            "Unable to load 'inputfail.txt'.",
        ));

        $args = ['foo.php', 'text', 'design', '--inputfile', 'inputfail.txt'];
        new CommandLineOptions($args);
    }

    /**
     * testHasVersionReturnsFalseByDefault
     * @throws Throwable
     */
    public function testHasVersionReturnsFalseByDefault(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode'];
        $opts = new CommandLineOptions($args);

        static::assertFalse($opts->hasVersion());
    }

    /**
     * testCliOptionsAcceptsVersionArgument
     * @throws Throwable
     */
    public function testCliOptionsAcceptsVersionArgument(): void
    {
        $args = [__FILE__, '--version'];
        $opts = new CommandLineOptions($args);

        static::assertTrue($opts->hasVersion());
    }

    /**
     * Tests if ignoreErrorsOnExit returns false by default
     * @throws Throwable
     */
    public function testIgnoreErrorsOnExitReturnsFalseByDefault(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode'];
        $opts = new CommandLineOptions($args);

        static::assertFalse($opts->ignoreErrorsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreErrorsOnExit argument
     * @throws Throwable
     */
    public function testCliOptionsAcceptsIgnoreErrorsOnExitArgument(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode', '--ignore-errors-on-exit'];
        $opts = new CommandLineOptions($args);

        static::assertTrue($opts->ignoreErrorsOnExit());
    }

    /**
     * Tests if CLI usage contains ignoreErrorsOnExit option
     * @throws Throwable
     */
    public function testCliUsageContainsIgnoreErrorsOnExitOption(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertStringContainsString('--ignore-errors-on-exit:', $opts->usage());
    }

    /**
     * Tests if ignoreViolationsOnExit returns false by default
     * @throws Throwable
     */
    public function testIgnoreViolationsOnExitReturnsFalseByDefault(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode'];
        $opts = new CommandLineOptions($args);

        static::assertFalse($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreViolationsOnExit argument
     * @throws Throwable
     */
    public function testCliOptionsAcceptsIgnoreViolationsOnExitArgument(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'unusedcode', '--ignore-violations-on-exit'];
        $opts = new CommandLineOptions($args);

        static::assertTrue($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI usage contains ignoreViolationsOnExit option
     * @throws Throwable
     */
    public function testCliUsageContainsIgnoreViolationsOnExitOption(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertStringContainsString('--ignore-violations-on-exit:', $opts->usage());
    }

    /**
     * Tests if CLI usage contains the auto-discovered renderers
     * @throws Throwable
     */
    public function testCliUsageContainsAutoDiscoveredRenderers(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertStringContainsString(
            'Available formats: ansi, baseline, checkstyle, github, gitlab, html, json, sarif, text, xml.',
            $opts->usage()
        );
    }

    /**
     * testCliUsageContainsStrictOption
     * @throws Throwable
     */
    public function testCliUsageContainsStrictOption(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertStringContainsString('--strict:', $opts->usage());
    }

    /**
     * testCliOptionsIsStrictReturnsFalseByDefault
     *
     * @throws Throwable
     * @since 1.2.0
     */
    public function testCliOptionsIsStrictReturnsFalseByDefault(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertFalse($opts->hasStrict());
    }

    /**
     * testCliOptionsAcceptsStrictArgument
     *
     * @throws Throwable
     * @since 1.2.0
     */
    public function testCliOptionsAcceptsStrictArgument(): void
    {
        $args = [__FILE__, '--strict', __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertTrue($opts->hasStrict());

        $args = [__FILE__, '--not-strict', __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertFalse($opts->hasStrict());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionsAcceptsMinimumpriorityArgument(): void
    {
        $args = [__FILE__, '--minimumpriority', '42', __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertSame(42, $opts->getMinimumPriority());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionsAcceptsMaximumpriorityArgument(): void
    {
        $args = [__FILE__, '--maximumpriority', '42', __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertSame(42, $opts->getMaximumPriority());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionGenerateBaselineFalseByDefault(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::None, $opts->generateBaseline());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionVerbosityNormal(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_NORMAL, $opts->getVerbosity());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionVerbosityVerbose(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '-v'];
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_VERBOSE, $opts->getVerbosity());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionVerbosityVeryVerbose(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '-vv'];
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_VERY_VERBOSE, $opts->getVerbosity());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionVerbosityDebug(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '-vvv'];
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_DEBUG, $opts->getVerbosity());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionGenerateBaselineShouldBeSet(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--generate-baseline'];
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::Generate, $opts->generateBaseline());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionUpdateBaselineShouldBeSet(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--update-baseline'];
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::Update, $opts->generateBaseline());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionBaselineFileShouldBeNullByDefault(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);
        static::assertNull($opts->baselineFile());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionBaselineFileShouldBeWithFilename(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--baseline-file', 'foobar.txt'];
        $opts = new CommandLineOptions($args);
        static::assertSame('foobar.txt', $opts->baselineFile());
    }

    /**
     * @throws Throwable
     */
    public function testGetMinimumPriorityReturnsLowestValueByDefault(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertSame(Rule::LOWEST_PRIORITY, $opts->getMinimumPriority());
    }

    /**
     * @throws Throwable
     */
    public function testGetCoverageReportReturnsNullByDefault(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertNull($opts->getCoverageReport());
    }

    /**
     * @throws Throwable
     */
    public function testGetCoverageReportWithCliOption(): void
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

        static::assertSame(__METHOD__, $opts->getCoverageReport());
    }

    /**
     * @throws Throwable
     */
    public function testGetCacheWithCliOption(): void
    {
        $opts = new CommandLineOptions(
            [
                __FILE__,
                __FILE__,
                'text',
                'codesize',
            ]
        );

        static::assertSame(ResultCacheStrategy::Content, $opts->cacheStrategy());
        static::assertFalse($opts->isCacheEnabled());

        $opts = new CommandLineOptions(
            [
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--cache',
                '--cache-strategy',
                ResultCacheStrategy::Timestamp->value,
            ]
        );

        static::assertSame(ResultCacheStrategy::Timestamp, $opts->cacheStrategy());
        static::assertTrue($opts->isCacheEnabled());

        $opts = new CommandLineOptions(
            [
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--cache',
                '--cache-strategy',
                ResultCacheStrategy::Content->value,
                '--cache-file',
                'abc',
            ]
        );

        static::assertSame(ResultCacheStrategy::Content, $opts->cacheStrategy());
        static::assertSame('abc', $opts->cacheFile());
        static::assertTrue($opts->isCacheEnabled());
    }

    /**
     * @throws Throwable
     */
    public function testExcludeOption(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--ignore', 'foo/bar', '--error-file', 'abc'];
        $opts = new CommandLineOptions($args);

        static::assertSame('abc', $opts->getErrorFile());
        static::assertSame('foo/bar', $opts->getIgnore());
        static::assertSame([
            'The --ignore option is deprecated, please use --exclude instead.',
        ], $opts->getDeprecations());

        $args = [__FILE__, __FILE__, 'text', 'codesize', '--exclude', 'bar/biz'];
        $opts = new CommandLineOptions($args);

        static::assertSame('bar/biz', $opts->getIgnore());
    }

    /**
     * @param class-string $expectedClass
     * @throws Throwable
     * @dataProvider dataProviderCreateRenderer
     * @covers \PHPMD\Renderer\RendererFactory::getRenderer
     */
    public function testCreateRenderer(string $reportFormat, $expectedClass): void
    {
        require_once self::$filesDirectory . '/PHPMD/Test/Renderer/NamespaceRenderer.php';

        require_once self::$filesDirectory . '/PHPMD/Test/Renderer/PEARRenderer.php';

        $args = [__FILE__, __FILE__, $reportFormat, 'codesize'];
        $opts = new CommandLineOptions($args);

        static::assertInstanceOf($expectedClass, $opts->createRenderer($reportFormat));
    }

    /**
     * @return list<mixed>
     */
    public static function dataProviderCreateRenderer(): array
    {
        return [
            ['html', HTMLRenderer::class],
            ['text', TextRenderer::class],
            ['xml', XMLRenderer::class],
            ['ansi', AnsiRenderer::class],
            ['github', GitHubRenderer::class],
            ['gitlab', GitLabRenderer::class],
            ['json', JSONRenderer::class],
            ['checkstyle', CheckStyleRenderer::class],
            ['sarif', SARIFRenderer::class],
            ['PHPMD_Test_Renderer_PEARRenderer', 'PHPMD_Test_Renderer_PEARRenderer'],
            ['PHPMD\\Test\\Renderer\\NamespaceRenderer', 'PHPMD\\Test\\Renderer\\NamespaceRenderer'],
            // Test what happens when class already exists.
            ['PHPMD\\Test\\Renderer\\NamespaceRenderer', 'PHPMD\\Test\\Renderer\\NamespaceRenderer'],
        ];
    }

    /**
     * @throws Throwable
     * @dataProvider dataProviderCreateRendererThrowsException
     * @covers \PHPMD\Renderer\RendererFactory::getCustomRenderer
     */
    public function testCreateRendererThrowsException(string $reportFormat, string $expectedExceptionMessage): void
    {
        self::expectExceptionObject(new InvalidArgumentException(
            $expectedExceptionMessage,
            code: RendererInterface::INPUT_ERROR,
        ));

        require_once self::$filesDirectory . '/PHPMD/Test/Renderer/InvalidRenderer.php';

        $args = [__FILE__, __FILE__, $reportFormat, 'codesize'];
        $opts = new CommandLineOptions($args);
        $opts->createRenderer();
    }

    /**
     * @return list<mixed>
     */
    public static function dataProviderCreateRendererThrowsException(): array
    {
        $defaultExceptionMessage = 'No renderer supports the format "%s".';

        $notExistsRendererClass = 'PHPMD\\Test\\Renderer\\NotExistsRenderer';
        $invalidRendererClass = 'PHPMD\\Test\\Renderer\\InvalidRenderer';

        return [
            ['', sprintf($defaultExceptionMessage, '')],
            [$notExistsRendererClass, sprintf($defaultExceptionMessage, $notExistsRendererClass)],
            [
                $invalidRendererClass,
                sprintf(
                    'Renderer class "%s" does not implement "%s".',
                    $invalidRendererClass,
                    RendererInterface::class
                ),
            ],
        ];
    }

    /**
     * @throws Throwable
     * @throws Throwable
     * @dataProvider dataProviderDeprecatedCliOptions
     */
    public function testDeprecatedCliOptions(string $deprecatedName, string $newName, Closure $result): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', sprintf('--%s', $deprecatedName), '42'];
        $opts = new CommandLineOptions($args);

        static::assertSame(
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

        static::assertSame(
            [],
            $opts->getDeprecations()
        );
        $result($opts);
    }

    /**
     * @return list<mixed>
     */
    public static function dataProviderDeprecatedCliOptions(): array
    {
        return [
            ['extensions', 'suffixes', static function (CommandLineOptions $opts): void {
                self::assertSame('42', $opts->getExtensions());
            }],
            ['ignore', 'exclude', static function (CommandLineOptions $opts): void {
                self::assertSame('42', $opts->getIgnore());
            }],
        ];
    }

    /**
     * @param list<string> $options
     * @param list<mixed> $expected
     * @throws Throwable
     * @throws Throwable
     * @dataProvider dataProviderGetReportFiles
     */
    public function testGetReportFiles(array $options, array $expected): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', ...$options];
        $opts = new CommandLineOptions($args);

        static::assertEquals($expected, $opts->getReportFiles());
    }

    /**
     * @throws Throwable
     */
    public function testCliOptionExtraLineInExcerptShouldBeWithNumber(): void
    {
        $args = [__FILE__, __FILE__, 'text', 'codesize', '--extra-line-in-excerpt', '5'];
        $opts = new CommandLineOptions($args);
        static::assertSame(5, $opts->extraLineInExcerpt());
    }

    /**
     * @return list<list<mixed>>
     */
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
