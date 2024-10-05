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
    public function testShouldFindFileFromCLI(): void
    {
        $args = ['script', 'source', 'xml', 'phpmd.xml', '--baseline-file', 'foobar.txt'];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertSame('foobar.txt', $finder->find());
    }

    /**
     * @covers ::existingFile
     * @covers ::find
     */
    public function testShouldFindExistingFileNearRuleSet(): void
    {
        $args = ['script', 'source', 'xml', static::createResourceUriForTest('testA/phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));

        // ensure consistent slashes
        $path = realpath(static::createResourceUriForTest('testA/phpmd.baseline.xml'));
        static::assertNotFalse($path);
        $expected = str_replace('\\', '/', $path);
        $actual = str_replace('\\', '/', $finder->existingFile()->find() ?: '');

        static::assertSame($expected, $actual);
    }

    /**
     * @covers ::find
     * @covers ::nullOrThrow
     */
    public function testShouldThrowExceptionForNonExistingRuleSet(): void
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to determine the baseline file location.',
        ));

        $args = ['script', 'source', 'xml', static::createResourceUriForTest('phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        $finder->notNull()->find();
    }

    /**
     * @covers ::find
     * @covers ::nullOrThrow
     */
    public function testShouldReturnNullForNonExistingRuleSet(): void
    {
        $args = ['script', 'source', 'xml', static::createResourceUriForTest('phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertNull($finder->find());
    }

    /**
     * @covers ::existingFile
     * @covers ::find
     * @covers ::nullOrThrow
     */
    public function testShouldOnlyFindExistingFile(): void
    {
        $args = ['script', 'source', 'xml', static::createResourceUriForTest('testB/phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertNull($finder->existingFile()->find());
    }

    /**
     * @covers ::find
     * @covers ::notNull
     * @covers ::nullOrThrow
     */
    public function testShouldThrowExceptionWhenFileIsNull(): void
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to find the baseline file',
        ));

        $args = ['script', 'source', 'xml', static::createResourceUriForTest('testB/phpmd.xml')];
        $finder = new BaselineFileFinder(new CommandLineOptions($args));
        static::assertNull($finder->existingFile()->notNull()->find());
    }
}
