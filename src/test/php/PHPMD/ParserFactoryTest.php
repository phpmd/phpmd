<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2009, Manuel Pichler <mapi@phpmd.org>.
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

namespace PHPMD;

/**
 * Test case for the parser factory class.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\ParserFactory
 * @group phpmd
 * @group unittest
 */
class ParserFactoryTest extends AbstractTest
{
    /**
     * testFactoryConfiguresInputDirectory
     *
     * @return void
     */
    public function testFactoryConfiguresInputDirectory()
    {
        $factory = new ParserFactory();
        
        $uri = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMock('PHPMD\\PHPMD', array('getInput'));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $ruleSet = $this->getRuleSetMock('PHPMD\\Node\\ClassNode');
        
        $parser = $factory->create($phpmd);
        $parser->addRuleSet($ruleSet);
        $parser->parse($this->getReportMock(0));
    }

    /**
     * testFactoryConfiguresInputFile
     *
     * @return void
     */
    public function testFactoryConfiguresInputFile()
    {
        $factory = new ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMock('PHPMD\\PHPMD', array('getInput'));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $ruleSet = $this->getRuleSetMock('PHPMD\\Node\\ClassNode');

        $parser = $factory->create($phpmd);
        $parser->addRuleSet($ruleSet);
        $parser->parse($this->getReportMock(0));
    }

    /**
     * testFactoryConfiguresMultipleInputDirectories
     *
     * @return void
     */
    public function testFactoryConfiguresMultipleInputDirectories()
    {
        $factory = new ParserFactory();

        $uri1 = $this->createFileUri('ParserFactory/File');
        $uri2 = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMock('PHPMD\\PHPMD', array('getInput'));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri1 . ',' . $uri2));

        $ruleSet = $this->getRuleSetMock('PHPMD\\Node\\ClassNode', 2);

        $parser = $factory->create($phpmd);
        $parser->addRuleSet($ruleSet);
        $parser->parse($this->getReportMock(0));
    }

    /**
     * testFactoryConfiguresMultipleInputFilesAndDirectories
     *
     * @return void
     */
    public function testFactoryConfiguresMultipleInputFilesAndDirectories()
    {
        $factory = new ParserFactory();

        $uri1 = $this->createFileUri('ParserFactory/File/Test.php');
        $uri2 = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMock('PHPMD\\PHPMD', array('getInput'));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri1 . ',' . $uri2));

        $ruleSet = $this->getRuleSetMock('PHPMD\\Node\\ClassNode', 2);

        $parser = $factory->create($phpmd);
        $parser->addRuleSet($ruleSet);
        $parser->parse($this->getReportMock(0));
    }

    /**
     * testFactoryConfiguresIgnorePattern
     *
     * @return void
     */
    public function testFactoryConfiguresIgnorePattern()
    {
        $factory = new ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMock('PHPMD\\PHPMD', array('getIgnorePattern', 'getInput'));
        $phpmd->expects($this->exactly(2))
            ->method('getIgnorePattern')
            ->will($this->returnValue(array('Test')));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $factory->create($phpmd);
    }

    /**
     * testFactoryConfiguresFileExtensions
     *
     * @return void
     */
    public function testFactoryConfiguresFileExtensions()
    {
        $factory = new ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMock('PHPMD\\PHPMD', array('getFileExtensions', 'getInput'));
        $phpmd->expects($this->exactly(2))
            ->method('getFileExtensions')
            ->will($this->returnValue(array('.php')));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $factory->create($phpmd);
    }
}
