<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTest;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineSetFactory
 */
class BaselineSetFactoryTest extends AbstractTest
{
    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldSucceed()
    {
        $filename = static::createResourceUriForTest('baseline.xml');
        $baseDir  = dirname($filename);
        $set      = BaselineSetFactory::fromFile($filename);

        static::assertTrue($set->contains('PHPMD\Rule\CleanCode\MissingImport', $baseDir . '/src/foo/bar', null));
        static::assertTrue(
            $set->contains('PHPMD\Rule\CleanCode\UndefinedVariable', $baseDir . '/src/foo/bar', 'myMethod')
        );
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldSucceedWithBackAndForwardSlashes()
    {
        $filename = static::createResourceUriForTest('baseline.xml');
        $baseDir  = dirname($filename);
        $set      = BaselineSetFactory::fromFile($filename);

        static::assertTrue($set->contains('PHPMD\Rule\CleanCode\MissingImport', $baseDir . '/src\\foo/bar', null));
        static::assertTrue(
            $set->contains('PHPMD\Rule\CleanCode\UndefinedVariable', $baseDir . '/src\\foo/bar', 'myMethod')
        );
    }

    /**
     * @covers ::fromFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to locate the baseline file at
     */
    public function testFromFileShouldThrowExceptionForMissingFile()
    {
        BaselineSetFactory::fromFile('foobar.xml');
    }

    /**
     * @covers ::fromFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to read xml from
     */
    public function testFromFileShouldThrowExceptionForOnInvalidXML()
    {
        BaselineSetFactory::fromFile(static::createResourceUriForTest('invalid-baseline.xml'));
    }

    /**
     * @covers ::fromFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Missing `rule` attribute in `violation`
     */
    public function testFromFileViolationMissingRuleShouldThrowException()
    {
        BaselineSetFactory::fromFile(static::createResourceUriForTest('missing-rule-baseline.xml'));
    }

    /**
     * @covers ::fromFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Missing `file` attribute in `violation` in
     */
    public function testFromFileViolationMissingFileShouldThrowException()
    {
        BaselineSetFactory::fromFile(static::createResourceUriForTest('missing-file-baseline.xml'));
    }
}
