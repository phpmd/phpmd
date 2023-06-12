<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheConfig;

class ResultCacheEngine
{
    /** @var ResultCacheConfig */
    private $config;

    /** @var ResultCacheFileFilter */
    private $fileFilter;

    /** @var ResultCacheUpdater */
    private $updater;

    /** @var ResultCacheWriter */
    private $writer;

    public function __construct(ResultCacheConfig $config, ResultCacheFileFilter $fileFilter, ResultCacheUpdater $updater, ResultCacheWriter $writer)
    {
        $this->config     = $config;
        $this->fileFilter = $fileFilter;
        $this->updater    = $updater;
        $this->writer     = $writer;
    }

    /**
     * @return ResultCacheConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return ResultCacheFileFilter
     */
    public function getFileFilter()
    {
        return $this->fileFilter;
    }

    /**
     * @return ResultCacheUpdater
     */
    public function getUpdater()
    {
        return $this->updater;
    }

    /**
     * @return ResultCacheWriter
     */
    public function getWriter()
    {
        return $this->writer;
    }
}
