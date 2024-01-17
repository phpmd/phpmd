<?php

namespace PHPMD\Cache;

use org\bovigo\vfs\vfsStream;
use PHPMD\AbstractTestCase;
use PHPMD\Rule\CleanCode\DuplicatedArrayKey;
use PHPMD\RuleSet;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheKeyFactory
 * @covers ::__construct
 */
class ResultCacheKeyFactoryTest extends AbstractTestCase
{
    /** @var ResultCacheKeyFactory */
    private $factory;

    protected function setUp(): void
    {
        $basePath = vfsStream::setup()->url();
        file_put_contents($basePath . '/composer.json', 'composer.json');
        file_put_contents($basePath . '/composer.lock', 'composer.lock');
        file_put_contents($basePath . '/baseline.xml', 'baseline.xml');
        $this->factory = new ResultCacheKeyFactory($basePath, $basePath . '/baseline.xml');
    }

    /**
     * @covers ::create
     * @covers ::getBaselineHash
     * @covers ::createRuleHashes
     * @covers ::getComposerHashes
     */
    public function testCreate()
    {
        $rule    = new DuplicatedArrayKey();
        $ruleSet = new RuleSet();
        $ruleSet->addRule($rule);

        $keyData = $this->factory->create(true, [$ruleSet])->toArray();

        static::assertArrayHasKey('strict', $keyData);
        static::assertArrayHasKey('composer', $keyData);
        static::assertArrayHasKey('rules', $keyData);
        static::assertArrayHasKey('phpVersion', $keyData);

        static::assertTrue($keyData['strict']);
        static::assertNotNull($keyData['baselineHash']);
        static::assertSame(['PHPMD\Rule\CleanCode\DuplicatedArrayKey'], array_keys($keyData['rules']));
        static::assertSame(['composer.json', 'composer.lock'], array_keys($keyData['composer']));
        static::assertSame(PHP_VERSION_ID, $keyData['phpVersion']);
    }
}
