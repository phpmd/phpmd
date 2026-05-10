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

use InvalidArgumentException;
use PHPMD\AbstractTestCase;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Cache\Model\ResultCacheStrategy;
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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test case for the {@link \PHPMD\TextUI\CommandLineOptions} class.
 */
#[CoversClass(CommandLineOptions::class)]
class CommandLineOptionsTest extends AbstractTestCase
{
    /**
     * @param array<string, list<string>|string|true> $args
     */
    private function createInput(array $args): ArrayInput
    {
        return new ArrayInput($args, (new Command())->getDefinition());
    }

    /**
     * testAssignsInputArgumentToInputProperty
     *
     * @since 1.1.0
     */
    public function testAssignsInputArgumentToInputProperty(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['design']]);
        $opts = new CommandLineOptions($args);

        static::assertEquals([__FILE__], $opts->getInputPaths());
    }

    /**
     * @since 2.14.0
     */
    public function testVerbose(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['design']]);
        $opts = new CommandLineOptions($args);
        $output = new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);
        $renderer = $opts->createRenderer($output);

        $verbosityExtractor = new ReflectionProperty(TextRenderer::class, 'verbosityLevel');
        $verbosityExtractor->setAccessible(true);

        $verbosityLevel = $verbosityExtractor->getValue($renderer);

        static::assertSame(OutputInterface::VERBOSITY_DEBUG, $verbosityLevel);
    }

    /**
     * @since 2.14.0
     */
    public function testColored(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['design']]);
        $opts = new CommandLineOptions($args);
        $output = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        $renderer = $opts->createRenderer($output);

        $coloredExtractor = new ReflectionProperty(TextRenderer::class, 'colored');
        $coloredExtractor->setAccessible(true);

        $colored = $coloredExtractor->getValue($renderer);

        static::assertTrue($colored);
    }

    /**
     * @since 2.14.0
     */
    public function testStdInDashShortCut(): void
    {
        $args = $this->createInput(['paths' => ['-'], '--format' => 'text', '--ruleset' => ['design']]);
        $opts = new CommandLineOptions($args);

        static::assertSame(['php://stdin'], $opts->getInputPaths());
    }

    /**
     * @since 2.14.0
     */
    public function testMultipleFiles(): void
    {
        // What happen when calling: phpmd src/*Service.php text design
        $args = $this->createInput(['paths' => ['src/FooService.php', 'src/BarService.php'], '--format' => 'text', '--ruleset' => ['design']]);
        $opts = new CommandLineOptions($args);

        static::assertSame(['src/FooService.php', 'src/BarService.php'], $opts->getInputPaths());
        static::assertSame('text', $opts->getReportFormat());
        static::assertSame(['design'], $opts->getRuleSets());
    }

    /**
     * testAssignsFormatArgumentToReportFormatProperty
     *
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentToReportFormatProperty(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['design']]);
        $opts = new CommandLineOptions($args);

        static::assertSame('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentToRuleSetProperty
     *
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentToRuleSetProperty(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['design']]);
        $opts = new CommandLineOptions($args);

        static::assertSame(['design'], $opts->getRuleSets());
    }

    /**
     * @since 3.0.0
     */
    public function testArgumentsAreAllOptional(): void
    {
        $args = $this->createInput(['paths' => ['app'], '--format' => 'sarif', '--ruleset' => ['design']]);
        $options = new CommandLineOptions($args);

        static::assertSame(['design'], $options->getRuleSets());
        static::assertSame('sarif', $options->getReportFormat());
        static::assertSame(['app'], $options->getInputPaths());

        $args = $this->createInput(['paths' => ['app']]);
        $options = new CommandLineOptions($args);

        static::assertSame(['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode', 'cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode'], $options->getRuleSets());
        static::assertSame('text', $options->getReportFormat());
        static::assertSame(['app'], $options->getInputPaths());
    }

    /**
     * testAssignsInputFileOptionToInputPathProperty
     *
     * @since 1.1.0
     */
    public function testAssignsInputFileOptionToInputPathProperty(): void
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = $this->createInput(['--format' => 'text', '--ruleset' => ['design'], '--input-file' => $uri]);
        $opts = new CommandLineOptions($args);

        static::assertSame(['Dir1/Class1.php', 'Dir2/Class2.php'], $opts->getInputPaths());
    }

    /**
     * testAssignsFormatArgumentCorrectWhenCalledWithInputFile
     *
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentCorrectWhenCalledWithInputFile(): void
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = $this->createInput(['paths' => ['foo.php'], '--format' => 'text', '--ruleset' => ['design'], '--input-file' => $uri]);
        $opts = new CommandLineOptions($args);

        static::assertSame('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile
     *
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile(): void
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = $this->createInput(['paths' => ['foo.php'], '--format' => 'text', '--ruleset' => ['design'], '--input-file' => $uri]);
        $opts = new CommandLineOptions($args);

        static::assertSame(['design'], $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenInputFileNotExists
     *
     * @since 1.1.0
     */
    public function testThrowsExpectedExceptionWhenInputFileNotExists(): void
    {
        self::expectExceptionObject(new InvalidArgumentException(
            "Unable to load 'inputfail.txt'.",
        ));

        $args = $this->createInput(['paths' => ['foo.php'], '--format' => 'text', '--ruleset' => ['design'], '--input-file' => 'inputfail.txt']);
        new CommandLineOptions($args);
    }

    /**
     * Tests if ignoreErrorsOnExit returns false by default
     */
    public function testIgnoreErrorsOnExitReturnsFalseByDefault(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['unusedcode']]);
        $opts = new CommandLineOptions($args);

        static::assertFalse($opts->ignoreErrorsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreErrorsOnExit argument
     */
    public function testCliOptionsAcceptsIgnoreErrorsOnExitArgument(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['unusedcode'], '--ignore-errors-on-exit' => true]);
        $opts = new CommandLineOptions($args);

        static::assertTrue($opts->ignoreErrorsOnExit());
    }

    /**
     * Tests if ignoreViolationsOnExit returns false by default
     */
    public function testIgnoreViolationsOnExitReturnsFalseByDefault(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['unusedcode']]);
        $opts = new CommandLineOptions($args);

        static::assertFalse($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreViolationsOnExit argument
     */
    public function testCliOptionsAcceptsIgnoreViolationsOnExitArgument(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['unusedcode'], '--ignore-violations-on-exit' => true]);
        $opts = new CommandLineOptions($args);

        static::assertTrue($opts->ignoreViolationsOnExit());
    }

    /**
     * testCliOptionsIsStrictReturnsFalseByDefault
     *
     * @since 1.2.0
     */
    public function testCliOptionsIsStrictReturnsFalseByDefault(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);

        static::assertFalse($opts->hasStrict());
    }

    /**
     * testCliOptionsAcceptsStrictArgument
     *
     * @since 1.2.0
     */
    public function testCliOptionsAcceptsStrictArgument(): void
    {
        $args = $this->createInput(['--strict' => true, 'paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);

        static::assertTrue($opts->hasStrict());
    }

    public function testCliOptionsAcceptsMinimumpriorityArgument(): void
    {
        $args = $this->createInput(['--minimum-priority' => '42', 'paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);

        static::assertSame(42, $opts->getMinimumPriority());
    }

    public function testCliOptionsAcceptsMaximumpriorityArgument(): void
    {
        $args = $this->createInput(['--maximum-priority' => '42', 'paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);

        static::assertSame(42, $opts->getMaximumPriority());
    }

    public function testCliOptionGenerateBaselineFalseByDefault(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::None, $opts->generateBaseline());
    }

    public function testCliOptionGenerateBaselineShouldBeSet(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize'], '--generate-baseline' => true]);
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::Generate, $opts->generateBaseline());
    }

    public function testCliOptionUpdateBaselineShouldBeSet(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize'], '--update-baseline' => true]);
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::Update, $opts->generateBaseline());
    }

    public function testCliOptionBaselineFileShouldBeNullByDefault(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);
        static::assertNull($opts->baselineFile());
    }

    public function testCliOptionBaselineFileShouldBeWithFilename(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize'], '--baseline-file' => 'foobar.txt']);
        $opts = new CommandLineOptions($args);
        static::assertSame('foobar.txt', $opts->baselineFile());
    }

    public function testGetMinimumPriorityReturnsLowestValueByDefault(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);

        static::assertSame(Rule::LOWEST_PRIORITY, $opts->getMinimumPriority());
    }

    public function testGetCoverageReportReturnsNullByDefault(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);

        static::assertNull($opts->getCoverageReport());
    }

    public function testGetCoverageReportWithCliOption(): void
    {
        $opts = new CommandLineOptions(
            $this->createInput([
                'paths' => [__FILE__],
                '--format' => 'text',
                '--ruleset' => ['codesize'],
                '--coverage' => __METHOD__,
            ])
        );

        static::assertSame(__METHOD__, $opts->getCoverageReport());
    }

    public function testGetCacheWithCliOption(): void
    {
        $opts = new CommandLineOptions(
            $this->createInput([
                'paths' => [__FILE__],
                '--format' => 'text',
                '--ruleset' => ['codesize'],
            ])
        );

        static::assertSame(ResultCacheStrategy::Content, $opts->cacheStrategy());
        static::assertFalse($opts->isCacheEnabled());

        $opts = new CommandLineOptions(
            $this->createInput([
                'paths' => [__FILE__],
                '--format' => 'text',
                '--ruleset' => ['codesize'],
                '--cache' => true,
                '--cache-strategy' => ResultCacheStrategy::Timestamp->value,
            ])
        );

        static::assertSame(ResultCacheStrategy::Timestamp, $opts->cacheStrategy());
        static::assertTrue($opts->isCacheEnabled());

        $opts = new CommandLineOptions(
            $this->createInput([
                'paths' => [__FILE__],
                '--format' => 'text',
                '--ruleset' => ['codesize'],
                '--cache' => true,
                '--cache-strategy' => ResultCacheStrategy::Content->value,
                '--cache-file' => 'abc',
            ])
        );

        static::assertSame(ResultCacheStrategy::Content, $opts->cacheStrategy());
        static::assertSame('abc', $opts->cacheFile());
        static::assertTrue($opts->isCacheEnabled());
    }

    public function testExcludeOption(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize'], '--exclude' => ['bar/biz']]);
        $opts = new CommandLineOptions($args);

        static::assertSame(['bar/biz'], $opts->getExcludePatterns());
    }

    /**
     * @param class-string $expectedClass
     *
     * @covers \PHPMD\Renderer\RendererFactory::getRenderer
     */
    #[DataProvider('dataProviderCreateRenderer')]
    public function testCreateRenderer(string $reportFormat, $expectedClass): void
    {
        require_once self::$filesDirectory . '/PHPMD/Test/Renderer/NamespaceRenderer.php';

        require_once self::$filesDirectory . '/PHPMD/Test/Renderer/PEARRenderer.php';

        $args = $this->createInput(['paths' => [__FILE__], '--format' => $reportFormat, '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);

        static::assertInstanceOf($expectedClass, $opts->createRenderer(new NullOutput(), $reportFormat));
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
     * @covers \PHPMD\Renderer\RendererFactory::getCustomRenderer
     */
    #[DataProvider('dataProviderCreateRendererThrowsException')]
    public function testCreateRendererThrowsException(string $reportFormat, string $expectedExceptionMessage): void
    {
        self::expectExceptionObject(new InvalidArgumentException(
            $expectedExceptionMessage,
            code: RendererInterface::INPUT_ERROR,
        ));

        require_once self::$filesDirectory . '/PHPMD/Test/Renderer/InvalidRenderer.php';

        $args = $this->createInput(['paths' => [__FILE__], '--format' => $reportFormat, '--ruleset' => ['codesize']]);
        $opts = new CommandLineOptions($args);
        $opts->createRenderer(new NullOutput());
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
     * @param array<string, string> $options
     * @param list<mixed> $expected
     */
    #[DataProvider('dataProviderGetReportFiles')]
    public function testGetReportFiles(array $options, array $expected): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize']] + $options);
        $opts = new CommandLineOptions($args);

        static::assertEquals($expected, $opts->getReportFiles());
    }

    /**
     * @return list<list<mixed>>
     */
    public static function dataProviderGetReportFiles(): array
    {
        return [
            [
                ['--reportfile-xml' => __FILE__],
                ['xml' => __FILE__],
            ],
            [
                ['--reportfile-html' => __FILE__],
                ['html' => __FILE__],
            ],
            [
                ['--reportfile-text' => __FILE__],
                ['text' => __FILE__],
            ],
            [
                ['--reportfile-github' => __FILE__],
                ['github' => __FILE__],
            ],
            [
                ['--reportfile-gitlab' => __FILE__],
                ['gitlab' => __FILE__],
            ],
            [
                [
                    '--reportfile-text' => __FILE__,
                    '--reportfile-xml' => __FILE__,
                    '--reportfile-html' => __FILE__,
                    '--reportfile-github' => __FILE__,
                    '--reportfile-gitlab' => __FILE__,
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

    public function testCliOptionExtraLineInExcerptShouldBeWithNumber(): void
    {
        $args = $this->createInput(['paths' => [__FILE__], '--format' => 'text', '--ruleset' => ['codesize'], '--extra-line-in-excerpt' => '5']);
        $opts = new CommandLineOptions($args);
        static::assertSame(5, $opts->extraLineInExcerpt());
    }
}
