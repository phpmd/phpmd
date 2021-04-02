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
     * @covers ::concat
     */
    public function testConcatShouldConcatTwoPaths()
    {
        static::assertSame('/foo/bar', Paths::concat('/foo', '/bar'));
    }

    /**
     * @covers ::concat
     */
    public function testConcatShouldDeduplicateSlashes()
    {
        static::assertSame('/foo/bar', Paths::concat('/foo/', '/bar'));
    }

    /**
     * @covers ::concat
     */
    public function testConcatShouldForwardAllSlashes()
    {
        static::assertSame('/foo/bar/text.txt', Paths::concat('/foo\\', '/bar\\text.txt'));
    }

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
     * @covers ::getAbsolutePath
     * @expectedException RuntimeException
     */
    public function testGetAbsolutePathShouldReturnNullForIrregularStream()
    {
        Paths::getAbsolutePath(fopen('php://stdout', 'wb'));
    }

    /**
     * @covers ::getAbsolutePath
     */
    public function testGetAbsolutePathShouldReturnPath()
    {
        $path = static::createResourceUriForTest('resource.txt');
        static::assertSame(realpath($path), Paths::getAbsolutePath(fopen($path, 'rb')));
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
