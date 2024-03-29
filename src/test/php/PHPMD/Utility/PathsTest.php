<?php

namespace PHPMD\Utility;

use PHPMD\AbstractTest;
use RuntimeException;

/**
 * @coversDefaultClass \PHPMD\Utility\Paths
 */
class PathsTest extends AbstractTest
{
    /**
     * @covers ::getRelativePath
     */
    public function testGetRelativePathShouldSubtractBasePath()
    {
        static::assertSame('bar/', Paths::getRelativePath('/foo', '/foo/bar/'));
    }

    /**
     * @covers ::getRelativePath
     */
    public function testGetRelativePathShouldTreatForwardAndBackwardSlashes()
    {
        static::assertSame('text.txt', Paths::getRelativePath('\\foo/bar\\', '/foo\\bar/text.txt'));
    }

    /**
     * @covers ::getRelativePath
     */
    public function testGetRelativePathShouldNotSubtractOnInfixPath()
    {
        static::assertSame('/foo/bar/text.txt', Paths::getRelativePath('/bar', '/foo/bar/text.txt'));
    }

    /**
     * @covers ::concat
     */
    public function testConcat()
    {
        static::assertSame('pathA/pathB', Paths::concat('pathA', 'pathB'));
        static::assertSame('pathA/pathB', Paths::concat('pathA', '/pathB'));
        static::assertSame('pathA/pathB', Paths::concat('pathA/', '/pathB'));
        static::assertSame('/file/pathA/pathB/example.txt', Paths::concat('\\file\\pathA\\', '\\pathB\\example.txt'));
    }

    /**
     * @covers ::getRealPath
     */
    public function testGetRealPathShouldReturnTheRealPath()
    {
        $path = static::createResourceUriForTest('resource.txt');
        static::assertSame(realpath($path), Paths::getRealPath($path));
    }

    /**
     * @covers ::getRealPath
     * @expectedException RuntimeException
     */
    public function testGetRealPathShouldThrowExceptionOnFailure()
    {
        Paths::getRealPath('unknown/path');
    }
}
