<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheConfig;
use PHPMD\RuleSet;
use PHPMD\TextUI\CommandLineOptions;

class ResultCacheEngineFactory
{
    /**
     * @param string    $basePath
     * @param RuleSet[] $ruleSetList
     * @return ResultCacheEngine|null
     */
    public static function create($basePath, CommandLineOptions $options, array $ruleSetList)
    {
        if ($options->isCacheEnabled() === false) {
            return null;
        }

        $cacheKeyFactory = new ResultCacheKeyFactory();
        $cacheKey        = $cacheKeyFactory->create($ruleSetList);

        $config = new ResultCacheConfig($options->cacheFile(), $options->cacheStrategy());
        $state  = ResultCacheStateFactory::fromFile($config->getFilePath());

        // the cache key doesn't match the stored cache key. Invalidate cache
        if ($state !== null && $state->getCacheKey()->isEqualTo($cacheKey) === false) {
            $state = null;
        }

        return new ResultCacheEngine(
            $config,
            new ResultCacheFileFilter($basePath, $config->getStrategy(), $cacheKey, $state),
            new ResultCacheUpdater($basePath),
            new ResultCacheWriter($config->getFilePath())
        );
    }
}
