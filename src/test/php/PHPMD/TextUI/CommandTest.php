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

use PHPMD\AbstractTest;

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
     * @param $sourceFile
     * @param $expectedExitCode
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
                (array) $options
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
                Command::EXIT_SUCCESS
            ),
            array(
                'source/source_with_npath_violation.php',
                Command::EXIT_VIOLATION
            ),
            array(
                'source/source_with_npath_violation.php',
                Command::EXIT_SUCCESS,
                array('--ignore-violations-on-exit')
            ),
            array(
                'source/ccn_suppress_function.php',
                Command::EXIT_VIOLATION,
                array('--strict')
            ),
            array(
                'source/ccn_suppress_function.php',
                Command::EXIT_SUCCESS
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
        );

        Command::main($args);

        $this->assertFileExists($xml);
        $this->assertFileExists($html);
        $this->assertFileExists($text);
        $this->assertFileExists($json);
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
            array('--exclude', 'ccn_,npath_'),
        );
    }

    /*
     * @return void
     */
    public function testMainWritesExceptionMessageToStderr()
    {
        stream_filter_register('stderr_stream', 'PHPMD\\TextUI\\StreamFilter');

        $this->stderrStreamFilter = stream_filter_prepend(STDERR, 'stderr_stream');

        Command::main(
            array(
                __FILE__,
                self::createFileUri('source/source_with_npath_violation.php'),
                "''",
                'naming'
            )
        );

        $this->assertContains(
            'Can\'t find the custom report class: ',
            StreamFilter::$streamHandle
        );
    }

    /*
     * @return void
     */
    public function testMainPrintsVersionToStdout()
    {
        stream_filter_register('stderr_stream', 'PHPMD\\TextUI\\StreamFilter');

        $this->stderrStreamFilter = stream_filter_prepend(STDOUT, 'stderr_stream');

        Command::main(
            array(
                __FILE__,
                '--version'
            )
        );

        $data = @parse_ini_file(__DIR__ . '/../../../../../build.properties');
        $version = $data['project.version'];

        $this->assertEquals('PHPMD ' . $version, trim(StreamFilter::$streamHandle));
    }
}
