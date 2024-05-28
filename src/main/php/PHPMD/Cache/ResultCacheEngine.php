<?php

namespace PHPMD\Cache;

class ResultCacheEngine
{
    public function __construct(
        private readonly ResultCacheFileFilter $fileFilter,
        private readonly ResultCacheUpdater $updater,
        private readonly ResultCacheWriter $writer,
    ) {
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
