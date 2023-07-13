<?php

namespace PHPMD\Cache;

class ResultCacheEngine
{
    /** @var ResultCacheFileFilter */
    private $fileFilter;

    /** @var ResultCacheUpdater */
    private $updater;

    /** @var ResultCacheWriter */
    private $writer;

    public function __construct(
        ResultCacheFileFilter $fileFilter,
        ResultCacheUpdater    $updater,
        ResultCacheWriter     $writer
    ) {
    
        $this->fileFilter = $fileFilter;
        $this->updater    = $updater;
        $this->writer     = $writer;
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
