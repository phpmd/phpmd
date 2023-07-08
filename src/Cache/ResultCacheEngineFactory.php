<?php

namespace PHPMD\Cache;

use PHPMD\RuleSet;
use PHPMD\TextUI\CommandLineOptions;

class ResultCacheEngineFactory
{
    /** @var ResultCacheKeyFactory */
    private $cacheKeyFactory;
    /** @var ResultCacheStateFactory */
    private $cacheStateFactory;

    public function __construct(ResultCacheKeyFactory $cacheKeyFactory, ResultCacheStateFactory $cacheStateFactory)
    {
        $this->cacheKeyFactory   = $cacheKeyFactory;
        $this->cacheStateFactory = $cacheStateFactory;
    }

    /**
     * @param string    $basePath
     * @param RuleSet[] $ruleSetList
     * @return ResultCacheEngine|null
     */
    public function create($basePath, CommandLineOptions $options, array $ruleSetList)
    {
        if ($options->isCacheEnabled() === false) {
            return null;
        }

        // create cache key based on the current rules and environment
        $cacheKey = $this->cacheKeyFactory->create($options->hasStrict(), $ruleSetList);

        // load result cache from file
        $state = $this->cacheStateFactory->fromFile($options->cacheFile());

        // the cache key doesn't match the stored cache key. Invalidate cache
        if ($state !== null && $state->getCacheKey()->isEqualTo($cacheKey) === false) {
            $state = null;
        }

        return new ResultCacheEngine(
            new ResultCacheFileFilter($basePath, $options->cacheStrategy(), $cacheKey, $state),
            new ResultCacheUpdater($basePath),
            new ResultCacheWriter($options->cacheFile())
        );
    }
}
