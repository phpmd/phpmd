<?php

namespace PHPMD\Cache\Model;

use PHPMD\AbstractTestCase;

/**
 * @coversDefaultClass \PHPMD\Cache\Model\ResultCacheKey
 * @covers ::__construct
 */
class ResultCacheKeyTest extends AbstractTestCase
{
    /**
     * @covers ::toArray
     */
    public function testToArray(): void
    {
        $key = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );
        $expected = [
            'strict' => true,
            'baselineHash' => 'baselineHash',
            'rules' => ['rule A' => 'hash1'],
            'composer' => ['composer.json' => 'hash2'],
            'phpVersion' => 12345,
        ];

        static::assertSame($expected, $key->toArray());
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualTo(): void
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );

        static::assertTrue($keyA->isEqualTo($keyB));
        static::assertTrue($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffStrict(): void
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );
        $keyB = new ResultCacheKey(
            false,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffRules(): void
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash2'],
            ['composer.json' => 'hash2'],
            12345
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffComposer(): void
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash1'],
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffPhpVersion(): void
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHash',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            54321
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffBaselineHash(): void
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHashA',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHashB',
            ['rule A' => 'hash1'],
            ['composer.json' => 'hash2'],
            12345
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }
}
