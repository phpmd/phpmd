<?php

namespace PHPMD\Utility;

use PHPMD\AbstractTestCase;
use RuntimeException;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Utility\Paths
 */
class PathsTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     * @covers ::getRelativePath
     */
    public function testGetRelativePathShouldSubtractBasePath(): void
    {
        static::assertSame('bar/', Paths::getRelativePath('/foo', '/foo/bar/'));
    }

    /**
     * @throws Throwable
     * @covers ::getRelativePath
     */
    public function testGetRelativePathShouldTreatForwardAndBackwardSlashes(): void
    {
        static::assertSame('text.txt', Paths::getRelativePath('\\foo/bar\\', '/foo\\bar/text.txt'));
    }

    /**
     * @throws Throwable
     * @covers ::getRelativePath
     */
    public function testGetRelativePathShouldNotSubtractOnInfixPath(): void
    {
        static::assertSame('/foo/bar/text.txt', Paths::getRelativePath('/bar', '/foo/bar/text.txt'));
    }

    /**
     * @throws Throwable
     * @covers ::concat
     */
    public function testConcat(): void
    {
        static::assertSame('pathA/pathB', Paths::concat('pathA', 'pathB'));
        static::assertSame('pathA/pathB', Paths::concat('pathA', '/pathB'));
        static::assertSame('pathA/pathB', Paths::concat('pathA/', '/pathB'));
        static::assertSame('/file/pathA/pathB/example.txt', Paths::concat('\\file\\pathA\\', '\\pathB\\example.txt'));
    }

    /**
     * @throws Throwable
     * @covers ::getRealPath
     */
    public function testGetRealPathShouldReturnTheRealPath(): void
    {
        $path = static::createResourceUriForTest('resource.txt');
        static::assertSame(realpath($path), Paths::getRealPath($path));
    }

    /**
     * @throws Throwable
     * @covers ::getRealPath
     */
    public function testGetRealPathShouldThrowExceptionOnFailure(): void
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to determine the realpath for: unknown/path',
        ));

        Paths::getRealPath('unknown/path');
    }
}
