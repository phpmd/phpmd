<?php

namespace PHPMD\Cache;

class ResultCacheConfig
{
    /** @var bool */
    private $enabled;
    /** @var string */
    private $filePath;
    /** @var string */
    private $strategy;

    /**
     * @param bool $enabled
     * @param string $filePath
     * @param string $strategy
     */
    public function __construct($enabled, $filePath, $strategy) {
        $this->enabled = $enabled;
        $this->filePath = $filePath;
        $this->strategy = $strategy;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }
}
