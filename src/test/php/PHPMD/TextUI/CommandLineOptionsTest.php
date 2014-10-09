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
 * Test case for the {@link \PHPMD\TextUI\CommandLineOptions} class.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\TextUI\CommandLineOptions
 * @group phpmd
 * @group phpmd::textui
 * @group unittest
 */
class CommandLineOptionsTest extends AbstractTest
{
    /**
     * testAssignsInputArgumentToInputProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsInputArgumentToInputProperty()
    {
        $args = array('foo.php', __FILE__, 'text', 'design');
        $opts = new CommandLineOptions($args);

        self::assertEquals(__FILE__, $opts->getInputPath());
    }

    /**
     * testAssignsFormatArgumentToReportFormatProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentToReportFormatProperty()
    {
        $args = array('foo.php', __FILE__, 'text', 'design');
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
        $args = array('foo.php', __FILE__, 'text', 'design');
        $opts = new CommandLineOptions($args);

        self::assertEquals('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenRequiredArgumentsNotSet
     * 
     * @return void
     * @since 1.1.0
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExpectedExceptionWhenRequiredArgumentsNotSet()
    {
        $args = array(__FILE__, 'text', 'design');
        new CommandLineOptions($args);
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

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('Dir1/Class1.php,Dir2/Class2.php', $opts->getInputPath());
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

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('text', $opts->getReportFormat());
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

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('design', $opts->getRuleSets());
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
        $args = array('foo.php', 'text', 'design', '--inputfile', 'inputfail.txt');
        new CommandLineOptions($args);
    }

    /**
     * testCliOptionsAcceptsVersionArgument
     *
     * @return void
     */
    public function testHasVersionReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode');
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
        $args = array(__FILE__, '--version');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->hasVersion());
    }

    /**
     * testCliUsageContainsStrictOption
     * 
     * @return void
     */
    public function testCliUsageContainsStrictOption()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertContains('--strict:', $opts->usage());
    }

    /**
     * testCliOptionsIsStrictReturnsFalseByDefault
     * 
     * @return void
     * @since 1.2.0
     */
    public function testCliOptionsIsStrictReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
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
        $args = array(__FILE__, '--strict', __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->hasStrict());
    }
}
