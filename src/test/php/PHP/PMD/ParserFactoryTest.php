<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
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
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://phpmd.org
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/PMD.php';
require_once 'PHP/PMD/ParserFactory.php';

/**
 * Test case for the parser factory class.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://phpmd.org
 *
 * @covers PHP_PMD_ParserFactory
 * @group phpmd
 * @group unittest
 */
class PHP_PMD_ParserFactoryTest extends PHP_PMD_AbstractTest
{
    /**
     * testFactoryConfiguresInputDirectory
     *
     * @return void
     */
    public function testFactoryConfiguresInputDirectory()
    {
        $factory = new PHP_PMD_ParserFactory();
        
        $uri = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMock('PHP_PMD');
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $ruleSet = $this->getRuleSetMock('PHP_PMD_Node_Class');
        
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
        $factory = new PHP_PMD_ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMock('PHP_PMD');
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $ruleSet = $this->getRuleSetMock('PHP_PMD_Node_Class');

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
        $factory = new PHP_PMD_ParserFactory();

        $uri1 = $this->createFileUri('ParserFactory/File');
        $uri2 = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMock('PHP_PMD', array('getInput'));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri1 . ',' . $uri2));

        $ruleSet = $this->getRuleSetMock('PHP_PMD_Node_Class', 2);

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
        $factory = new PHP_PMD_ParserFactory();

        $uri1 = $this->createFileUri('ParserFactory/File/Test.php');
        $uri2 = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMock('PHP_PMD', array('getInput'));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri1 . ',' . $uri2));

        $ruleSet = $this->getRuleSetMock('PHP_PMD_Node_Class', 2);

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
        $factory = new PHP_PMD_ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMock('PHP_PMD');
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
        $factory = new PHP_PMD_ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMock('PHP_PMD');
        $phpmd->expects($this->exactly(2))
            ->method('getFileExtensions')
            ->will($this->returnValue(array('.php')));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $factory->create($phpmd);
    }
}
