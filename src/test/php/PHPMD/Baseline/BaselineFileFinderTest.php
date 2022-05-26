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
     * @covers ::tryFind
     */
    public function testShouldFindFileFromCLI()
    {
        $args   = array('script', 'source', 'xml', 'phpmd.xml', '--baseline-file', 'foobar.txt');
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertSame('foobar.txt', $finder->find());
    }

    /**
     * @covers ::find
     * @covers ::tryFind
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
     * @covers ::tryFind
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to determine the baseline file location.
     */
    public function testShouldReturnNullForNonExistingRuleSet()
    {
        $args   = array('script', 'source', 'xml', static::createResourceUriForTest('phpmd.xml'));
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        $finder->find();
    }

    /**
     * @covers ::find
     * @covers ::tryFind
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
     * @covers ::tryFind
     * @covers ::notNull
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
