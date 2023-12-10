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
    public function testToArray()
    {
        $key      = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );
        $expected = array(
            'strict'       => true,
            'baselineHash' => 'baselineHash',
            'rules'        => array('rule A' => 'hash1'),
            'composer'     => array('composer.json' => 'hash2'),
            'phpVersion'   => 12345
        );

        static::assertSame($expected, $key->toArray());
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualTo()
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );

        static::assertTrue($keyA->isEqualTo($keyB));
        static::assertTrue($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffStrict()
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );
        $keyB = new ResultCacheKey(
            false,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffRules()
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash2'),
            array('composer.json' => 'hash2'),
            12345
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffComposer()
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash1'),
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffPhpVersion()
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHash',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            54321
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }

    /**
     * @covers ::isEqualTo
     */
    public function testIsEqualToDiffBaselineHash()
    {
        $keyA = new ResultCacheKey(
            true,
            'baselineHashA',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );
        $keyB = new ResultCacheKey(
            true,
            'baselineHashB',
            array('rule A' => 'hash1'),
            array('composer.json' => 'hash2'),
            12345
        );

        static::assertFalse($keyA->isEqualTo($keyB));
        static::assertFalse($keyB->isEqualTo($keyA));
    }
}
