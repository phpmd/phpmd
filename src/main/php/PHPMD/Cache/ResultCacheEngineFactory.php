<?php

namespace PHPMD\Cache;

class ResultCacheEngineFactory
{
    /**
     * @param string $basePath
     * @return ResultCacheEngine|null
     */
    public static function create($basePath)
    {
        $config = new ResultCacheConfig(true, $basePath . '/.phpmd.result-cache.php', 'content');
        if ($config->isEnabled()) {
            return null;
        }

        $state   = ResultCacheStateFactory::fromFile($config->getFilePath());
        return new ResultCacheEngine(
            $config,
            new ResultCacheFileFilter($basePath, $config->getStrategy(), $state),
            new ResultCacheUpdater($basePath),
            new ResultCacheWriter($config->getFilePath())
        );
    }
}
