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

        $factory = new BaselineSetFactory();
        $set     = $factory->fromFile($filename);

        static::assertTrue($set->contains('PHPMD\Rule\CleanCode\MissingImport', 'src/foo/bar'));
        static::assertTrue($set->contains('PHPMD\Rule\CleanCode\UndefinedVariable', 'src/foo/bar'));
    }

    /**
     * @covers ::fromFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown file
     */
    public function testFromFileShouldThrowExceptionForMissingFile()
    {
        $factory = new BaselineSetFactory();
        $factory->fromFile('foobar.xml');
    }

    /**
     * @covers ::fromFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to read xml from
     */
    public function testFromFileShouldThrowExceptionForOnInvalidXML()
    {
        $factory = new BaselineSetFactory();
        $factory->fromFile(static::createResourceUriForTest('invalid-baseline.xml'));
    }

    /**
     * @covers ::fromFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Missing `rule` attribute in `violation`
     */
    public function testFromFileViolationMissingRuleShouldThrowException()
    {
        $factory = new BaselineSetFactory();
        $factory->fromFile(static::createResourceUriForTest('missing-rule-baseline.xml'));
    }

    /**
     * @covers ::fromFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Missing `file` attribute in `violation` in
     */
    public function testFromFileViolationMissingFileShouldThrowException()
    {
        $factory = new BaselineSetFactory();
        $factory->fromFile(static::createResourceUriForTest('missing-file-baseline.xml'));
    }
}
