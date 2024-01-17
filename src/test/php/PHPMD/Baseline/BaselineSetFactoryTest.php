<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTestCase;
use RuntimeException;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineSetFactory
 */
class BaselineSetFactoryTest extends AbstractTestCase
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
     */
    public function testFromFileShouldThrowExceptionForMissingFile()
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to locate the baseline file at',
        ));

        BaselineSetFactory::fromFile('foobar.xml');
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldThrowExceptionForOnInvalidXML()
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to read xml from',
        ));

        BaselineSetFactory::fromFile(static::createResourceUriForTest('invalid-baseline.xml'));
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileViolationMissingRuleShouldThrowException()
    {
        self::expectExceptionObject(new RuntimeException(
            'Missing `rule` attribute in `violation`',
        ));

        BaselineSetFactory::fromFile(static::createResourceUriForTest('missing-rule-baseline.xml'));
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileViolationMissingFileShouldThrowException()
    {
        self::expectExceptionObject(new RuntimeException(
            'Missing `file` attribute in `violation` in',
        ));

        BaselineSetFactory::fromFile(static::createResourceUriForTest('missing-file-baseline.xml'));
    }
}
