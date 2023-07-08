<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTest;
use PHPMD\TextUI\CommandLineOptions;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineFileFinder
 * @covers ::__construct
 */
class BaselineFileFinderTest extends AbstractTest
{
    /**
     * @covers ::find
     */
    public function testShouldFindFileFromCLI()
    {
        $args   = array('script', 'source', 'xml', 'phpmd.xml', '--baseline-file', 'foobar.txt');
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertSame('foobar.txt', $finder->find());
    }

    /**
     * @covers ::find
     * @covers ::existingFile
     */
    public function testShouldFindExistingFileNearRuleSet()
    {
        $args   = array('script', 'source', 'xml', static::createResourceUriForTest('testA/phpmd.xml'));
        $finder = new BaselineFileFinder(new CommandLineOptions($args));

        // ensure consistent slashes
        $expected = str_replace("\\", "/", realpath(static::createResourceUriForTest('testA/phpmd.baseline.xml')));
        $actual   = str_replace("\\", "/", $finder->existingFile()->find());

        static::assertSame($expected, $actual);
    }

    /**
     * @covers ::find
     * @covers ::nullOrThrow
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to determine the baseline file location.
     */
    public function testShouldThrowExceptionForNonExistingRuleSet()
    {
        $args   = array('script', 'source', 'xml', static::createResourceUriForTest('phpmd.xml'));
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        $finder->notNull()->find();
    }

    /**
     * @covers ::find
     * @covers ::nullOrThrow
     */
    public function testShouldReturnNullForNonExistingRuleSet()
    {
        $args   = array('script', 'source', 'xml', static::createResourceUriForTest('phpmd.xml'));
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertNull($finder->find());
    }

    /**
     * @covers ::find
     * @covers ::nullOrThrow
     * @covers ::existingFile
     */
    public function testShouldOnlyFindExistingFile()
    {
        $args   = array('script', 'source', 'xml', static::createResourceUriForTest('testB/phpmd.xml'));
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertNull($finder->existingFile()->find());
    }

    /**
     * @covers ::find
     * @covers ::notNull
     * @covers ::nullOrThrow
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to find the baseline file
     */
    public function testShouldThrowExceptionWhenFileIsNull()
    {
        $args   = array('script', 'source', 'xml', static::createResourceUriForTest('testB/phpmd.xml'));
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertNull($finder->existingFile()->notNull()->find());
    }
}
