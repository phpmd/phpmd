<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTestCase;
use PHPMD\Rule\CleanCode\MissingImport;
use PHPMD\Rule\CleanCode\UndefinedVariable;
use RuntimeException;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineSetFactory
 */
class BaselineSetFactoryTest extends AbstractTestCase
{
    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldSucceed(): void
    {
        $filename = static::createResourceUriForTest('baseline.xml');
        $baseDir = dirname($filename);
        $set = BaselineSetFactory::fromFile($filename);

        static::assertTrue($set->contains(MissingImport::class, $baseDir . '/src/foo/bar', null));
        static::assertTrue(
            $set->contains(UndefinedVariable::class, $baseDir . '/src/foo/bar', 'myMethod')
        );
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldSucceedWithBackAndForwardSlashes(): void
    {
        $filename = static::createResourceUriForTest('baseline.xml');
        $baseDir = dirname($filename);
        $set = BaselineSetFactory::fromFile($filename);

        static::assertTrue($set->contains(MissingImport::class, $baseDir . '/src\\foo/bar', null));
        static::assertTrue(
            $set->contains(UndefinedVariable::class, $baseDir . '/src\\foo/bar', 'myMethod')
        );
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldThrowExceptionForMissingFile(): void
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to locate the baseline file at',
        ));

        BaselineSetFactory::fromFile('foobar.xml');
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldThrowExceptionForOnInvalidXML(): void
    {
        self::expectExceptionObject(new RuntimeException(
            'Unable to read xml from',
        ));

        BaselineSetFactory::fromFile(static::createResourceUriForTest('invalid-baseline.xml'));
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileViolationMissingRuleShouldThrowException(): void
    {
        self::expectExceptionObject(new RuntimeException(
            'Missing `rule` attribute in `violation`',
        ));

        BaselineSetFactory::fromFile(static::createResourceUriForTest('missing-rule-baseline.xml'));
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileViolationMissingFileShouldThrowException(): void
    {
        self::expectExceptionObject(new RuntimeException(
            'Missing `file` attribute in `violation` in',
        ));

        BaselineSetFactory::fromFile(static::createResourceUriForTest('missing-file-baseline.xml'));
    }
}
