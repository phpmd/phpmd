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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD\TextUI;

use PHPMD\AbstractTestCase;
use PHPMD\Utility\Paths;

/**
 * Test case for the {@link \PHPMD\TextUI\Command} class.
 *
 * @covers \PHPMD\TextUI\Command
 */
class CommandTest extends AbstractTestCase
{
    /** @var resource */
    private $stderrStreamFilter;

    protected function tearDown(): void
    {
        if (is_resource($this->stderrStreamFilter)) {
            stream_filter_remove($this->stderrStreamFilter);
        }
        $this->stderrStreamFilter = null;

        parent::tearDown();
    }

    /**
     * @dataProvider dataProviderTestMainWithOption
     */
    public function testMainStrictOptionIsOfByDefault(
        string $sourceFile,
        ExitCode $expectedExitCode,
        ?array $options = null
    ): void {
        $args = array_filter(
            [
                __FILE__,
                self::createFileUri($sourceFile),
                'html',
                'codesize',
                '--reportfile',
                self::createTempFileUri(),
                ...($options ?? []),
            ]
        );

        $exitCode = Command::main($args);
        static::assertEquals($expectedExitCode, $exitCode);
    }

    public static function dataProviderTestMainWithOption(): array
    {
        return [
            [
                'source/source_without_violations.php',
                ExitCode::Success,
            ],
            [
                'source/source_with_npath_violation.php',
                ExitCode::Violation,
            ],
            [
                'source/source_with_npath_violation.php',
                ExitCode::Success,
                ['--ignore-violations-on-exit'],
            ],
            [
                'source/source_with_npath_violation.php',
                ExitCode::Violation,
                ['--ignore-errors-on-exit'],
            ],
            [
                'source/source_with_parse_error.php',
                ExitCode::Error,
            ],
            [
                'source/source_with_parse_error.php',
                ExitCode::Error,
                ['--ignore-violations-on-exit'],
            ],
            [
                'source/source_with_parse_error.php',
                ExitCode::Success,
                ['--ignore-errors-on-exit'],
            ],
            [
                'source',
                ExitCode::Error,
            ],
            [
                'source',
                ExitCode::Error,
                ['--ignore-violations-on-exit'],
            ],
            [
                'source',
                ExitCode::Violation,
                ['--ignore-errors-on-exit'],
            ],
            [
                'source',
                ExitCode::Success,
                ['--ignore-errors-on-exit', '--ignore-violations-on-exit'],
            ],
            [
                'source/ccn_suppress_function.php',
                ExitCode::Violation,
                ['--strict'],
            ],
            [
                'source/ccn_suppress_function.php',
                ExitCode::Success,
            ],
        ];
    }

    public function testWithMultipleReportFiles(): void
    {
        $args = [
            __FILE__,
            self::createFileUri('source/source_with_npath_violation.php'),
            'xml',
            'design',
            '--reportfile',
            self::createTempFileUri(),
            '--reportfile-xml',
            $xml = self::createTempFileUri(),
            '--reportfile-html',
            $html = self::createTempFileUri(),
            '--reportfile-text',
            $text = self::createTempFileUri(),
            '--reportfile-json',
            $json = self::createTempFileUri(),
            '--reportfile-checkstyle',
            $checkstyle = self::createTempFileUri(),
            '--reportfile-sarif',
            $sarif = self::createTempFileUri(),
        ];

        Command::main($args);

        static::assertFileExists($xml);
        static::assertFileExists($html);
        static::assertFileExists($text);
        static::assertFileExists($json);
        static::assertFileExists($checkstyle);
        static::assertFileExists($sarif);
    }

    public function testOutput(): void
    {
        $uri = realpath(self::createFileUri('source/source_with_anonymous_class.php'));
        $temp = self::createTempFileUri();
        $exitCode = Command::main([
            __FILE__,
            $uri,
            'text',
            'naming',
            '--reportfile',
            $temp,
        ]);

        static::assertSame(ExitCode::Violation, $exitCode);
        static::assertSame(
            "$uri:8  ShortVariable  Avoid variables with short names like \$a. " .
            'Configured minimum length is 3.' . PHP_EOL,
            file_get_contents($temp)
        );
    }

    /**
     * @param string $option
     * @param string $value
     * @dataProvider dataProviderWithFilter
     */
    public function testWithFilter($option, $value): void
    {
        $args = [
            __FILE__,
            self::createFileUri('source/'),
            'text',
            'codesize',
            '--reportfile',
            self::createTempFileUri(),
            $option,
            $value,
        ];

        $exitCode = Command::main($args);
        static::assertEquals(ExitCode::Success, $exitCode);
    }

    public static function dataProviderWithFilter(): array
    {
        return [
            ['--suffixes', '.class.php'],
            ['--exclude', 'ccn_,*npath_,*parse_error'],
        ];
    }

    public function testMainGenerateBaseline(): void
    {
        $uri = str_replace('\\', '/', realpath(self::createFileUri('source/source_with_anonymous_class.php')));
        $temp = self::createTempFileUri();
        $exitCode = Command::main([
            __FILE__,
            $uri,
            'text',
            'naming',
            '--generate-baseline',
            '--baseline-file',
            $temp,
        ]);

        static::assertSame(ExitCode::Success, $exitCode);
        static::assertFileExists($temp);
        static::assertStringContainsString(Paths::getRelativePath(getcwd(), $uri), file_get_contents($temp));
    }

    /**
     * Testcase:
     * - Class has existing ShortVariable and new BooleanGetMethodName violations
     * - Baseline has ShortVariable and LongClassName baseline violations
     * Expect in baseline:
     * - LongClassName violation should be removed
     * - ShortVariable violation should still exist
     * - BooleanGetMethodName shouldn't be added
     */
    public function testMainUpdateBaseline(): void
    {
        $sourceTemp = self::createTempFileUri('ClassWithMultipleViolations.php');
        $baselineTemp = self::createTempFileUri();
        // set work directory to the temp dir
        self::changeWorkingDirectory(dirname($baselineTemp));

        copy(static::createResourceUriForTest('UpdateBaseline/ClassWithMultipleViolations.php'), $sourceTemp);
        copy(static::createResourceUriForTest('UpdateBaseline/phpmd.baseline.xml'), $baselineTemp);

        $exitCode = Command::main([
            __FILE__,
            $sourceTemp,
            'text',
            'naming',
            '--update-baseline',
            '--baseline-file',
            $baselineTemp,
        ]);

        static::assertSame(ExitCode::Success, $exitCode);
        static::assertXmlStringEqualsXmlString(
            file_get_contents(static::createResourceUriForTest('UpdateBaseline/expected.baseline.xml')),
            file_get_contents($baselineTemp)
        );
    }

    public function testMainBaselineViolationShouldBeIgnored(): void
    {
        $sourceFile = realpath(static::createResourceUriForTest('Baseline/ClassWithShortVariable.php'));
        $baselineFile = realpath(static::createResourceUriForTest('Baseline/phpmd.baseline.xml'));
        $exitCode = Command::main([
            __FILE__,
            $sourceFile,
            'text',
            'naming',
            '--baseline-file',
            $baselineFile,
        ]);

        static::assertSame(ExitCode::Success, $exitCode);
    }

    public function testMainWritesExceptionMessageToStderr(): void
    {
        stream_filter_register('stderr_stream', StreamFilter::class);

        $this->stderrStreamFilter = stream_filter_prepend(STDERR, 'stderr_stream');

        Command::main(
            [
                __FILE__,
                self::createFileUri('source/source_with_npath_violation.php'),
                "''",
                'naming',
            ]
        );

        static::assertStringContainsString(
            'Can\'t find the custom report class: ',
            StreamFilter::$streamHandle
        );
    }

    public function testMainWritesExceptionMessageToErrorFileIfSpecified(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'err');

        Command::main(
            [
                __FILE__,
                self::createFileUri('source/source_with_npath_violation.php'),
                "''",
                'naming',
                '--error-file',
                $file,
            ]
        );

        $errors = (string) file_get_contents($file);
        unlink($file);

        static::assertSame("Can't find the custom report class: ''" . PHP_EOL, $errors);

        $file = tempnam(sys_get_temp_dir(), 'err');

        Command::main(
            [
                __FILE__,
                self::createFileUri('source/source_with_npath_violation.php'),
                "''",
                'naming',
                '--error-file',
                $file,
                '-vvv',
            ]
        );

        $errors = (string) file_get_contents($file);
        unlink($file);

        static::assertStringStartsWith("Can't find the custom report class: ''" . PHP_EOL, $errors);
        static::assertMatchesRegularExpression(
            '`' . preg_quote(str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                'src/main/php/PHPMD/TextUI/CommandLineOptions.php:'
            ), '`') . '\d+' . PHP_EOL . '`',
            $errors
        );
        static::assertMatchesRegularExpression(
            '`' . preg_quote(str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                'src/main/php/PHPMD/TextUI/CommandLineOptions.php'
            ), '`') . '\(\d+\): PHPMD\\\\TextUI\\\\CommandLineOptions->createCustomRenderer\(\)`',
            $errors
        );
    }

    public function testOutputDeprecation(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'err');

        Command::main(
            [
                __FILE__,
                __FILE__,
                'text',
                'naming',
                '--ignore',
                'foobar',
                '--error-file',
                $file,
            ]
        );

        $errors = (string) file_get_contents($file);
        unlink($file);

        static::assertSame(
            'The --ignore option is deprecated, please use --exclude instead.' . PHP_EOL . PHP_EOL,
            $errors
        );
    }

    public function testMainPrintsVersionToStdout(): void
    {
        stream_filter_register('stderr_stream', StreamFilter::class);

        $this->stderrStreamFilter = stream_filter_prepend(STDOUT, 'stderr_stream');

        Command::main(
            [
                __FILE__,
                '--version',
            ]
        );

        $data = @parse_ini_file(__DIR__ . '/../../../../../build.properties');
        $version = $data['project.version'];

        static::assertEquals('PHPMD ' . $version, trim(StreamFilter::$streamHandle));
    }
}
