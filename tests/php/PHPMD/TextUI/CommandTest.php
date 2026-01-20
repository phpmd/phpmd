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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test case for the {@link \PHPMD\TextUI\Command} class.
 */
#[CoversClass(Command::class)]
class CommandTest extends AbstractTestCase
{
    /**
     * @param ?array<string> $options
     */
    #[DataProvider('dataProviderTestMainWithOption')]
    public function testMainStrictOptionIsOfByDefault(
        string $sourceFile,
        int $expectedExitCode,
        ?array $options = []
    ): void {
        $args = [
            'paths' => [self::createFileUri($sourceFile)],
            '--format' => 'html',
            '--ruleset' => ['codesize'],
            '--reportfile-html' => self::createTempFileUri(),
        ] + $options;

        $tester = new CommandTester(new Command());
        $exitCode = $tester->execute($args);
        static::assertEquals($expectedExitCode, $exitCode);
    }

    /**
     * @return list<list<mixed>>
     */
    public static function dataProviderTestMainWithOption(): array
    {
        return [
            [
                'source/source_without_violations.php',
                Command::SUCCESS,
            ],
            [
                'source/source_with_npath_violation.php',
                Command::INVALID,
            ],
            [
                'source/source_with_npath_violation.php',
                Command::SUCCESS,
                ['--ignore-violations-on-exit' => true],
            ],
            [
                'source/source_with_npath_violation.php',
                Command::INVALID,
                ['--ignore-errors-on-exit' => true],
            ],
            [
                'source/source_with_parse_error.php',
                Command::ERROR,
            ],
            [
                'source/source_with_parse_error.php',
                Command::ERROR,
                ['--ignore-violations-on-exit' => true],
            ],
            [
                'source/source_with_parse_error.php',
                Command::SUCCESS,
                ['--ignore-errors-on-exit' => true],
            ],
            [
                'source',
                Command::ERROR,
            ],
            [
                'source',
                Command::ERROR,
                ['--ignore-violations-on-exit' => true],
            ],
            [
                'source',
                Command::INVALID,
                ['--ignore-errors-on-exit' => true],
            ],
            [
                'source',
                Command::SUCCESS,
                ['--ignore-errors-on-exit' => true, '--ignore-violations-on-exit' => true],
            ],
            [
                'source/ccn_suppress_function.php',
                Command::INVALID,
                ['--strict' => true],
            ],
            [
                'source/ccn_suppress_function.php',
                Command::SUCCESS,
            ],
        ];
    }

    public function testWithMultipleReportFiles(): void
    {
        $xml = self::createTempFileUri();
        $html = self::createTempFileUri();
        $text = self::createTempFileUri();
        $json = self::createTempFileUri();
        $checkstyle = self::createTempFileUri();
        $sarif = self::createTempFileUri();

        $args = [
            'paths' => [self::createFileUri('source/source_with_npath_violation.php')],
            '--format' => 'xml',
            '--ruleset' => ['design'],
            '--reportfile-xml' => $xml,
            '--reportfile-html' => $html,
            '--reportfile-text' => $text,
            '--reportfile-json' => $json,
            '--reportfile-checkstyle' => $checkstyle,
            '--reportfile-sarif' => $sarif,
        ];

        $tester = new CommandTester(new Command());
        $tester->execute($args);

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
        static::assertIsString($uri);
        $temp = self::createTempFileUri();
        $tester = new CommandTester(new Command());
        $exitCode = $tester->execute([
            'paths' => [$uri],
            '--format' => 'text',
            '--ruleset' => ['naming'],
            '--reportfile-text' => $temp,
        ]);

        static::assertSame(Command::INVALID, $exitCode);
        static::assertSame(
            "$uri:8  ShortVariable  Avoid variables with short names like \$a. " .
            'Configured minimum length is 3.' . PHP_EOL,
            file_get_contents($temp)
        );
    }

    /**
     * @param list<string> $value
     */
    #[DataProvider('dataProviderWithFilter')]
    public function testWithFilter(string $option, array $value): void
    {
        $args = [
            'paths' => [self::createFileUri('source/')],
            '--format' => 'text',
            '--ruleset' => ['codesize'],
            '--reportfile-text' => self::createTempFileUri(),
            $option => $value,
        ];

        $tester = new CommandTester(new Command());
        $exitCode = $tester->execute($args);
        static::assertEquals(Command::SUCCESS, $exitCode);
    }

    /**
     * @return list<list<list<string>|string>>
     */
    public static function dataProviderWithFilter(): array
    {
        return [
            ['--suffixes', ['.class.php']],
            ['--exclude', ['ccn_', '*npath_', '*parse_error']],
        ];
    }

    public function testMainGenerateBaseline(): void
    {
        $path = realpath(self::createFileUri('source/source_with_anonymous_class.php'));
        static::assertIsString($path);
        $uri = str_replace('\\', '/', $path);
        $temp = self::createTempFileUri();
        $tester = new CommandTester(new Command());
        $exitCode = $tester->execute([
            'paths' => [$uri],
            '--format' => 'text',
            '--ruleset' => ['naming'],
            '--generate-baseline' => true,
            '--baseline-file' => $temp,
        ]);

        static::assertSame(Command::SUCCESS, $exitCode);
        static::assertFileExists($temp);
        $cwd = getcwd();
        static::assertIsString($cwd);
        $tempData = file_get_contents($temp);
        static::assertIsString($tempData);
        static::assertStringContainsString(Paths::getRelativePath($cwd, $uri), $tempData);
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

        $tester = new CommandTester(new Command());
        $exitCode = $tester->execute([
            'paths' => [$sourceTemp],
            '--format' => 'text',
            '--ruleset' => ['naming'],
            '--update-baseline' => true,
            '--baseline-file' => $baselineTemp,
        ]);

        static::assertSame(Command::SUCCESS, $exitCode);
        $expectedXml = file_get_contents(static::createResourceUriForTest('UpdateBaseline/expected.baseline.xml'));
        static::assertIsString($expectedXml);
        $actualXml = file_get_contents($baselineTemp);
        static::assertIsString($actualXml);
        static::assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }

    public function testMainBaselineViolationShouldBeIgnored(): void
    {
        $sourceFile = realpath(static::createResourceUriForTest('Baseline/ClassWithShortVariable.php'));
        static::assertIsString($sourceFile);
        $baselineFile = realpath(static::createResourceUriForTest('Baseline/phpmd.baseline.xml'));
        static::assertIsString($baselineFile);
        $tester = new CommandTester(new Command());
        $exitCode = $tester->execute([
            'paths' => [$sourceFile],
            '--format' => 'text',
            '--ruleset' => ['naming'],
            '--baseline-file' => $baselineFile,
        ]);

        static::assertSame(Command::SUCCESS, $exitCode);
    }

    public function testMainPrintsVersionToStdout(): void
    {
        $changelog = file_get_contents(__DIR__ . '/../../../../CHANGELOG', false, null, 0, 1024) ?: '';
        $version = preg_match('/phpmd-([\S]+)/', $changelog, $match) ? $match[1] : '@package_version@';

        static::assertEquals($version, Command::getVersion());
    }
}
