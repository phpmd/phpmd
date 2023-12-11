<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTestCase;
use PHPMD\TextUI\CommandLineOptions;
use RuntimeException;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineFileFinder
 * @covers ::__construct
 */
class BaselineFileFinderTest extends AbstractTestCase
{
    /**
     * @covers ::find
     */
    public function testShouldFindFileFromCLI()
    {
        $args   = ['script', 'source', 'xml', 'phpmd.xml', '--baseline-file', 'foobar.txt'];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertSame('foobar.txt', $finder->find());
    }

    /**
     * @covers ::find
     * @covers ::existingFile
     */
    public function testShouldFindExistingFileNearRuleSet()
    {
        $args   = ['script', 'source', 'xml', static::createResourceUriForTest('testA/phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));

        // ensure consistent slashes
        $expected = str_replace("\\", "/", realpath(static::createResourceUriForTest('testA/phpmd.baseline.xml')));
        $actual   = str_replace("\\", "/", $finder->existingFile()->find());

        static::assertSame($expected, $actual);
    }

    /**
     * @covers ::find
     * @covers ::nullOrThrow
     */
    public function testShouldThrowExceptionForNonExistingRuleSet()
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to determine the baseline file location.',
        ));

        $args   = ['script', 'source', 'xml', static::createResourceUriForTest('phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        $finder->notNull()->find();
    }

    /**
     * @covers ::find
     * @covers ::nullOrThrow
     */
    public function testShouldReturnNullForNonExistingRuleSet()
    {
        $args   = ['script', 'source', 'xml', static::createResourceUriForTest('phpmd.xml')];
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
        $args   = ['script', 'source', 'xml', static::createResourceUriForTest('testB/phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertNull($finder->existingFile()->find());
    }

    /**
     * @covers ::find
     * @covers ::notNull
     * @covers ::nullOrThrow
     */
    public function testShouldThrowExceptionWhenFileIsNull()
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to find the baseline file',
        ));

        $args   = ['script', 'source', 'xml', static::createResourceUriForTest('testB/phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertNull($finder->existingFile()->notNull()->find());
    }
}
