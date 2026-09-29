<?php

namespace PHPMD\Cache;

/**
 * @internal
 */
class ResultCacheEngine
{
    public function __construct(
        private readonly ResultCacheFileFilter $fileFilter,
        private readonly ResultCacheUpdater $updater,
        private readonly ResultCacheWriter $writer,
    ) {
    }

    public function getFileFilter(): ResultCacheFileFilter
    {
        return $this->fileFilter;
    }

    public function getUpdater(): ResultCacheUpdater
    {
        return $this->updater;
    }

    public function getWriter(): ResultCacheWriter
    {
        return $this->writer;
    }
}
