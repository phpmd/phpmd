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

use PHPMD\Node\ClassNode;

/**
 * Test case for the parser factory class.
 *
 * @covers \PHPMD\ParserFactory
 */
class ParserFactoryTest extends AbstractTestCase
{
    /**
     * testFactoryConfiguresInputDirectory
     */
    public function testFactoryConfiguresInputDirectory(): void
    {
        $factory = new ParserFactory();

        $uri = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMockFromBuilder(
            $this->getMockBuilder(PHPMD::class)
                ->onlyMethods(['getInput'])
        );
        $phpmd->expects($this->once())
            ->method('getInput')
            ->willReturn($uri);

        $ruleSet = $this->getRuleSetMock(ClassNode::class);

        $parser = $factory->create($phpmd);
        $parser->addRuleSet($ruleSet);
        $parser->parse($this->getReportWithNoViolation());
    }

    /**
     * testFactoryConfiguresInputFile
     */
    public function testFactoryConfiguresInputFile(): void
    {
        $factory = new ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMockFromBuilder(
            $this->getMockBuilder(PHPMD::class)
                ->onlyMethods(['getInput'])
        );
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri));

        $ruleSet = $this->getRuleSetMock(ClassNode::class);

        $parser = $factory->create($phpmd);
        $parser->addRuleSet($ruleSet);
        $parser->parse($this->getReportWithNoViolation());
    }

    /**
     * testFactoryConfiguresMultipleInputDirectories
     */
    public function testFactoryConfiguresMultipleInputDirectories(): void
    {
        $factory = new ParserFactory();

        $uri1 = $this->createFileUri('ParserFactory/File');
        $uri2 = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMockFromBuilder(
            $this->getMockBuilder(PHPMD::class)
                ->onlyMethods(['getInput'])
        );
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri1 . ',' . $uri2));

        $ruleSet = $this->getRuleSetMock(ClassNode::class, 2);

        $parser = $factory->create($phpmd);
        $parser->addRuleSet($ruleSet);
        $parser->parse($this->getReportWithNoViolation());
    }

    /**
     * testFactoryConfiguresMultipleInputFilesAndDirectories
     */
    public function testFactoryConfiguresMultipleInputFilesAndDirectories(): void
    {
        $factory = new ParserFactory();

        $uri1 = $this->createFileUri('ParserFactory/File/Test.php');
        $uri2 = $this->createFileUri('ParserFactory/Directory');

        $phpmd = $this->getMockFromBuilder($this->getMockBuilder(PHPMD::class)->onlyMethods(['getInput']));
        $phpmd->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($uri1 . ',' . $uri2));

        $ruleSet = $this->getRuleSetMock(ClassNode::class, 2);

        $parser = $factory->create($phpmd);
        $parser->addRuleSet($ruleSet);
        $parser->parse($this->getReportWithNoViolation());
    }

    /**
     * testFactoryConfiguresIgnorePattern
     */
    public function testFactoryConfiguresIgnorePattern(): void
    {
        $factory = new ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMockFromBuilder(
            $this->getMockBuilder(PHPMD::class)
                ->onlyMethods(['getIgnorePatterns', 'getInput'])
        );
        $phpmd->expects($this->exactly(2))
            ->method('getIgnorePatterns')
            ->willReturn(['Test']);
        $phpmd->expects($this->once())
            ->method('getInput')
            ->willReturn($uri);

        $factory->create($phpmd);
    }

    /**
     * testFactoryConfiguresFileExtensions
     */
    public function testFactoryConfiguresFileExtensions(): void
    {
        $factory = new ParserFactory();

        $uri = $this->createFileUri('ParserFactory/File/Test.php');

        $phpmd = $this->getMockFromBuilder(
            $this->getMockBuilder(PHPMD::class)
                ->onlyMethods(['getFileExtensions', 'getInput'])
        );
        $phpmd->expects($this->exactly(2))
            ->method('getFileExtensions')
            ->willReturn(['.php']);
        $phpmd->expects($this->once())
            ->method('getInput')
            ->willReturn($uri);

        $factory->create($phpmd);
    }
}
