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

namespace PHPMD;

/**
 * Test case for the parser factory class.
 *
 * @covers \PHPMD\ParserFactory
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

        $phpmd = $this->getMockBuilder('PHPMD\\PHPMD')->setMethods(array('getInput'))->getMock();
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

        $phpmd = $this->getMockBuilder('PHPMD\\PHPMD')->setMethods(array('getInput'))->getMock();
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

        $phpmd = $this->getMockBuilder('PHPMD\\PHPMD')->setMethods(array('getInput'))->getMock();
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

        $phpmd = $this->getMockBuilder('PHPMD\\PHPMD')->setMethods(array('getInput'))->getMock();
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

        $phpmd = $this->getMockBuilder('PHPMD\\PHPMD')->setMethods(array('getIgnorePattern', 'getInput'))->getMock();
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

        $phpmd = $this->getMockBuilder('PHPMD\\PHPMD')->setMethods(array('getFileExtensions', 'getInput'))->getMock();
        $phpmd->expects($this->exactly(2))
            ->method('getFileExtensions')
            ->will($this->returnValue(array('.php')));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $factory->create($phpmd);
    }
}
