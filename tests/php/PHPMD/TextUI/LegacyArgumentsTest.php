<?php

namespace PHPMD\TextUI;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PHPMD\TextUI\LegacyArguments
 */
class LegacyArgumentsTest extends TestCase
{
    /**
     * @covers ::isLegacyInvocation
     */
    public function testIsLegacyInvocationAcceptsEveryRendererFormat(): void
    {
        $formats = [
            'ansi', 'checkstyle', 'github', 'githubcheckruns', 'gitlab',
            'html', 'json', 'sarif', 'text', 'xml',
        ];

        foreach ($formats as $format) {
            static::assertTrue(
                LegacyArguments::isLegacyInvocation(['phpmd', 'src/', $format, 'cleancode']),
                sprintf('Format "%s" should be recognized as a legacy invocation', $format)
            );
        }
    }

    /**
     * @covers ::isLegacyInvocation
     */
    public function testIsLegacyInvocationRejectsUnknownFormat(): void
    {
        static::assertFalse(LegacyArguments::isLegacyInvocation(['phpmd', 'src/', 'yaml', 'cleancode']));
    }

    /**
     * @covers ::isLegacyInvocation
     */
    public function testIsLegacyInvocationRejectsAnalyzeSubCommand(): void
    {
        static::assertFalse(LegacyArguments::isLegacyInvocation(['phpmd', 'analyze', 'text', 'cleancode']));
    }

    /**
     * @covers ::isLegacyInvocation
     */
    public function testIsLegacyInvocationRejectsOptionAsFirstArgument(): void
    {
        static::assertFalse(LegacyArguments::isLegacyInvocation(['phpmd', '--version', 'text', 'cleancode']));
    }

    /**
     * @covers ::isLegacyInvocation
     */
    public function testIsLegacyInvocationRejectsOptionAsRulesetArgument(): void
    {
        static::assertFalse(LegacyArguments::isLegacyInvocation(['phpmd', 'src/', 'text', '--help']));
    }

    /**
     * @covers ::isLegacyInvocation
     */
    public function testIsLegacyInvocationRejectsTooFewArguments(): void
    {
        static::assertFalse(LegacyArguments::isLegacyInvocation(['phpmd', 'src/', 'text']));
    }

    /**
     * @covers ::transform
     */
    public function testTransformBuildsAnalyzeInvocation(): void
    {
        $result = LegacyArguments::transform(['phpmd', 'src/', 'text', 'cleancode']);

        static::assertSame(
            ['phpmd', 'analyze', 'src/', '--format', 'text', '--no-progress', '--ruleset', 'cleancode'],
            $result
        );
    }

    /**
     * @covers ::transform
     */
    public function testTransformSplitsCommaSeparatedFileList(): void
    {
        $result = LegacyArguments::transform(['phpmd', 'src/File.php,src/Other.php', 'text', 'cleancode']);

        static::assertSame(
            [
                'phpmd', 'analyze', 'src/File.php', 'src/Other.php', '--format', 'text', '--no-progress',
                '--ruleset', 'cleancode',
            ],
            $result
        );
    }

    /**
     * @covers ::transform
     */
    public function testTransformExpandsEveryRulesetIntoRepeatedOption(): void
    {
        $result = LegacyArguments::transform(['phpmd', 'src/', 'text', 'cleancode,codesize,naming']);

        static::assertSame(
            [
                'phpmd', 'analyze', 'src/', '--format', 'text', '--no-progress',
                '--ruleset', 'cleancode',
                '--ruleset', 'codesize',
                '--ruleset', 'naming',
            ],
            $result
        );
    }

    /**
     * @covers ::transform
     */
    public function testTransformExpandsCommaSeparatedExcludeAndSuffixes(): void
    {
        $result = LegacyArguments::transform(
            ['phpmd', 'src/', 'text', 'cleancode', '--exclude', 'vendor,tests', '--suffixes', 'php,phtml']
        );

        static::assertSame(
            [
                'phpmd', 'analyze', 'src/', '--format', 'text', '--no-progress', '--ruleset', 'cleancode',
                '--exclude', 'vendor',
                '--exclude', 'tests',
                '--suffixes', 'php',
                '--suffixes', 'phtml',
            ],
            $result
        );
    }

    /**
     * @covers ::transform
     */
    public function testTransformPassesUnknownTrailingArgumentsThrough(): void
    {
        $result = LegacyArguments::transform(
            ['phpmd', 'src/', 'text', 'cleancode', '--strict', '--reportfile', 'out.txt']
        );

        static::assertSame(
            [
                'phpmd', 'analyze', 'src/', '--format', 'text', '--no-progress', '--ruleset', 'cleancode',
                '--strict',
                '--reportfile', 'out.txt',
            ],
            $result
        );
    }

    /**
     * @covers ::transform
     */
    public function testTransformKeepsExcludeWithoutValueUnchanged(): void
    {
        $result = LegacyArguments::transform(['phpmd', 'src/', 'text', 'cleancode', '--exclude']);

        static::assertSame(
            ['phpmd', 'analyze', 'src/', '--format', 'text', '--no-progress', '--ruleset', 'cleancode', '--exclude'],
            $result
        );
    }

    /**
     * @covers ::transform
     */
    public function testTransformReproducesGrumphpCommandLine(): void
    {
        $result = LegacyArguments::transform(
            [
                'phpmd', 'src/File.php,src/Other.php', 'text', 'phpmd.xml',
                '--exclude', 'vendor,tests', '--suffixes', 'php',
            ]
        );

        static::assertSame(
            [
                'phpmd', 'analyze', 'src/File.php', 'src/Other.php', '--format', 'text', '--no-progress',
                '--ruleset', 'phpmd.xml',
                '--exclude', 'vendor',
                '--exclude', 'tests',
                '--suffixes', 'php',
            ],
            $result
        );
    }

    /**
     * @covers ::deprecationMessage
     */
    public function testDeprecationMessageNamesTheRequestedFormat(): void
    {
        $message = LegacyArguments::deprecationMessage(['phpmd', 'src/', 'checkstyle', 'cleancode']);

        static::assertStringContainsString('positional arguments are deprecated', $message);
        static::assertStringContainsString('--format checkstyle', $message);
    }
}
