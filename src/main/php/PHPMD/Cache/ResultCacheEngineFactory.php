<?php

namespace PHPMD\Cache;

use PHPMD\TextUI\CommandLineOptions;

class ResultCacheEngineFactory
{
    /**
     * @param string $basePath
     * @return ResultCacheEngine|null
     */
    public static function create($basePath, CommandLineOptions $options)
    {
        $config = new ResultCacheConfig($options->isCacheEnabled(), $options->cacheFile(), 'content');
        if ($config->isEnabled() === false) {
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
