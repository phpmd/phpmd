<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\TextUI;

use PHPMD\AbstractTest;

/**
 * Test case for the {@link \PHPMD\TextUI\Command} class.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PHPMD\TextUI\Command
 * @group unittest
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
        );

        Command::main($args);

        $this->assertFileExists($xml);
        $this->assertFileExists($html);
        $this->assertFileExists($text);
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
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM works different here.');
        }

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
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM works different here.');
        }

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
