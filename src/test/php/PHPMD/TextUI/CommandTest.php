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

use PHPMD\AbstractTest;
use PHPMD\Utility\Paths;

/**
 * Test case for the {@link \PHPMD\TextUI\Command} class.
 *
 * @covers \PHPMD\TextUI\Command
 */
class CommandTest extends AbstractTest
{
    /**
     * @var resource
     */
    private $stderrStreamFilter;

    /**
     * @return void
     */
    protected function tearDown()
    {
        if (is_resource($this->stderrStreamFilter)) {
            stream_filter_remove($this->stderrStreamFilter);
        }
        $this->stderrStreamFilter = null;

        parent::tearDown();
    }

    /**
     * @param            $sourceFile
     * @param            $expectedExitCode
     * @param array|null $options
     * @return void
     * @dataProvider dataProviderTestMainWithOption
     */
    public function testMainStrictOptionIsOfByDefault($sourceFile, $expectedExitCode, array $options = null)
    {
        $args = array_filter(
            array_merge(
                array(
                    __FILE__,
                    self::createFileUri($sourceFile),
                    'html',
                    'codesize',
                    '--reportfile',
                    self::createTempFileUri(),
                ),
                (array)$options
            )
        );

        $exitCode = Command::main($args);
        $this->assertEquals($expectedExitCode, $exitCode);
    }

    /**
     * @return array
     */
    public function dataProviderTestMainWithOption()
    {
        return array(
            array(
                'source/source_without_violations.php',
                Command::EXIT_SUCCESS,
            ),
            array(
                'source/source_with_npath_violation.php',
                Command::EXIT_VIOLATION,
            ),
            array(
                'source/source_with_npath_violation.php',
                Command::EXIT_SUCCESS,
                array('--ignore-violations-on-exit'),
            ),
            array(
                'source/source_with_npath_violation.php',
                Command::EXIT_VIOLATION,
                array('--ignore-errors-on-exit'),
            ),
            array(
                'source/source_with_parse_error.php',
                Command::EXIT_ERROR,
            ),
            array(
                'source/source_with_parse_error.php',
                Command::EXIT_ERROR,
                array('--ignore-violations-on-exit'),
            ),
            array(
                'source/source_with_parse_error.php',
                Command::EXIT_SUCCESS,
                array('--ignore-errors-on-exit'),
            ),
            array(
                'source',
                Command::EXIT_ERROR,
            ),
            array(
                'source',
                Command::EXIT_ERROR,
                array('--ignore-violations-on-exit'),
            ),
            array(
                'source',
                Command::EXIT_VIOLATION,
                array('--ignore-errors-on-exit'),
            ),
            array(
                'source',
                Command::EXIT_SUCCESS,
                array('--ignore-errors-on-exit', '--ignore-violations-on-exit'),
            ),
            array(
                'source/ccn_suppress_function.php',
                Command::EXIT_VIOLATION,
                array('--strict'),
            ),
            array(
                'source/ccn_suppress_function.php',
                Command::EXIT_SUCCESS,
            ),
        );
    }

    /**
     * @return void
     */
    public function testWithMultipleReportFiles()
    {
        $args = array(
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
        );

        Command::main($args);

        $this->assertFileExists($xml);
        $this->assertFileExists($html);
        $this->assertFileExists($text);
        $this->assertFileExists($json);
        $this->assertFileExists($checkstyle);
        $this->assertFileExists($sarif);
    }

    public function testOutput()
    {
        $uri      = realpath(self::createFileUri('source/source_with_anonymous_class.php'));
        $temp     = self::createTempFileUri();
        $exitCode = Command::main(array(
            __FILE__,
            $uri,
            'text',
            'naming',
            '--reportfile',
            $temp,
        ));

        $this->assertSame(Command::EXIT_VIOLATION, $exitCode);
        $this->assertSame(
            "$uri:8  ShortVariable  Avoid variables with short names like \$a. " .
            'Configured minimum length is 3.' . PHP_EOL,
            file_get_contents($temp)
        );
    }

    /**
     * @param string $option
     * @param string $value
     * @return void
     * @dataProvider dataProviderWithFilter
     */
    public function testWithFilter($option, $value)
    {
        $args = array(
            __FILE__,
            self::createFileUri('source/'),
            'text',
            'codesize',
            '--reportfile',
            self::createTempFileUri(),
            $option,
            $value,
        );

        $exitCode = Command::main($args);
        $this->assertEquals(Command::EXIT_SUCCESS, $exitCode);
    }

    /**
     * @return array
     */
    public function dataProviderWithFilter()
    {
        return array(
            array('--suffixes', '.class.php'),
            array('--exclude', 'ccn_,npath_,parse_error'),
        );
    }

    public function testMainGenerateBaseline()
    {
        $uri      = str_replace("\\", "/", realpath(self::createFileUri('source/source_with_anonymous_class.php')));
        $temp     = self::createTempFileUri();
        $exitCode = Command::main(array(
            __FILE__,
            $uri,
            'text',
            'naming',
            '--generate-baseline',
            '--baseline-file',
            $temp,
        ));

        static::assertSame(Command::EXIT_SUCCESS, $exitCode);
        static::assertFileExists($temp);
        static::assertContains(Paths::getRelativePath(getcwd(), $uri), file_get_contents($temp));
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
    public function testMainUpdateBaseline()
    {
        $sourceTemp   = self::createTempFileUri('ClassWithMultipleViolations.php');
        $baselineTemp = self::createTempFileUri();
        // set work directory to the temp dir
        self::changeWorkingDirectory(dirname($baselineTemp));

        copy(static::createResourceUriForTest('UpdateBaseline/ClassWithMultipleViolations.php'), $sourceTemp);
        copy(static::createResourceUriForTest('UpdateBaseline/phpmd.baseline.xml'), $baselineTemp);

        $exitCode = Command::main(array(
            __FILE__,
            $sourceTemp,
            'text',
            'naming',
            '--update-baseline',
            '--baseline-file',
            $baselineTemp,
        ));

        static::assertSame(Command::EXIT_SUCCESS, $exitCode);
        static::assertXmlStringEqualsXmlString(
            file_get_contents(static::createResourceUriForTest('UpdateBaseline/expected.baseline.xml')),
            file_get_contents($baselineTemp)
        );
    }

    public function testMainBaselineViolationShouldBeIgnored()
    {
        $sourceFile   = realpath(static::createResourceUriForTest('Baseline/ClassWithShortVariable.php'));
        $baselineFile = realpath(static::createResourceUriForTest('Baseline/phpmd.baseline.xml'));
        $exitCode     = Command::main(array(
            __FILE__,
            $sourceFile,
            'text',
            'naming',
            '--baseline-file',
            $baselineFile,
        ));

        static::assertSame(Command::EXIT_SUCCESS, $exitCode);
    }

    public function testMainWritesExceptionMessageToStderr()
    {
        stream_filter_register('stderr_stream', 'PHPMD\\TextUI\\StreamFilter');

        $this->stderrStreamFilter = stream_filter_prepend(STDERR, 'stderr_stream');

        Command::main(
            array(
                __FILE__,
                self::createFileUri('source/source_with_npath_violation.php'),
                "''",
                'naming',
            )
        );

        $this->assertContains(
            'Can\'t find the custom report class: ',
            StreamFilter::$streamHandle
        );
    }

    public function testMainWritesExceptionMessageToErrorFileIfSpecified()
    {
        $file = tempnam(sys_get_temp_dir(), 'err');

        Command::main(
            array(
                __FILE__,
                self::createFileUri('source/source_with_npath_violation.php'),
                "''",
                'naming',
                '--error-file',
                $file,
            )
        );

        $errors = (string)file_get_contents($file);
        unlink($file);

        $this->assertSame("Can't find the custom report class: ''" . PHP_EOL, $errors);

        $file = tempnam(sys_get_temp_dir(), 'err');

        Command::main(
            array(
                __FILE__,
                self::createFileUri('source/source_with_npath_violation.php'),
                "''",
                'naming',
                '--error-file',
                $file,
                '-vvv',
            )
        );

        $errors = (string)file_get_contents($file);
        unlink($file);

        $this->assertStringStartsWith("Can't find the custom report class: ''" . PHP_EOL, $errors);
        $this->assertContains(
            str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                'src/main/php/PHPMD/TextUI/CommandLineOptions.php:701'
            ) . PHP_EOL,
            $errors
        );
        $this->assertContains(
            str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                'src/main/php/PHPMD/TextUI/CommandLineOptions.php(603): '
            ) . 'PHPMD\\TextUI\\CommandLineOptions->createCustomRenderer()',
            $errors
        );
    }

    public function testOutputDeprecation()
    {
        $file = tempnam(sys_get_temp_dir(), 'err');

        Command::main(
            array(
                __FILE__,
                __FILE__,
                'text',
                'naming',
                '--ignore',
                'foobar',
                '--error-file',
                $file,
            )
        );

        $errors = (string)file_get_contents($file);
        unlink($file);

        $this->assertSame(
            'The --ignore option is deprecated, please use --exclude instead.' . PHP_EOL . PHP_EOL,
            $errors
        );
    }

    public function testMainPrintsVersionToStdout()
    {
        stream_filter_register('stderr_stream', 'PHPMD\\TextUI\\StreamFilter');

        $this->stderrStreamFilter = stream_filter_prepend(STDOUT, 'stderr_stream');

        Command::main(
            array(
                __FILE__,
                '--version',
            )
        );

        $data    = @parse_ini_file(__DIR__ . '/../../../../../build.properties');
        $version = $data['project.version'];

        $this->assertEquals('PHPMD ' . $version, trim(StreamFilter::$streamHandle));
    }
}
