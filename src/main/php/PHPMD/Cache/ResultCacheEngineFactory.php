<?php

namespace PHPMD\Cache;

use PHPMD\RuleSet;
use PHPMD\TextUI\CommandLineOptions;
use PHPMD\Utility\Output;

class ResultCacheEngineFactory
{
    /** @var Output */
    private $output;
    /** @var ResultCacheKeyFactory */
    private $cacheKeyFactory;
    /** @var ResultCacheStateFactory */
    private $cacheStateFactory;

    public function __construct(Output $output, ResultCacheKeyFactory $cacheKeyFactory, ResultCacheStateFactory $cacheStateFactory)
    {
        $this->output            = $output;
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
            $this->output->writeln('Cache is not enabled.', Output::VERBOSITY_VERY_VERBOSE);
            return null;
        }

        // create cache key based on the current rules and environment
        $cacheKey = $this->cacheKeyFactory->create($options->hasStrict(), $ruleSetList);

        // load result cache from file
        $state = $this->cacheStateFactory->fromFile($options->cacheFile());
        if ($state === null) {
            $this->output->writeln('Cache is enabled, but no prior cache-result file exists.', Output::VERBOSITY_VERY_VERBOSE);
        }

        // the cache key doesn't match the stored cache key. Invalidate cache
        if ($state !== null && $state->getCacheKey()->isEqualTo($cacheKey) === false) {
            $this->output->writeln('Cache is enabled, but the cache metadata doesnt match.', Output::VERBOSITY_VERY_VERBOSE);
            $state = null;
        }

        return new ResultCacheEngine(
            new ResultCacheFileFilter($basePath, $options->cacheStrategy(), $cacheKey, $state),
            new ResultCacheUpdater($basePath),
            new ResultCacheWriter($options->cacheFile())
        );
    }
}
