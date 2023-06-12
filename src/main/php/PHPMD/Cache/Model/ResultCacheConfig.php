<?php

namespace PHPMD\Cache\Model;

class ResultCacheConfig
{
    /** @var string */
    private $filePath;
    /** @var string */
    private $strategy;

    /**
     * @param string $filePath
     * @param string $strategy
     */
    public function __construct($filePath, $strategy)
    {
        $this->filePath = $filePath;
        $this->strategy = $strategy;
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
