<?php

namespace PHPMD\Cache;

use PDepend\Input\Filter;
use PHPMD\Utility\Paths;

class ResultCacheEngine implements Filter
{
    /** @var ResultCacheConfig */
    private $config;

    /** @var ResultCacheState */
    private $state;

    /** @var ResultCacheState */
    private $newState;

    /** @var string */
    private $basePath;

    /**
     * @param string $basePath
     */
    public function __construct($basePath, $config, $state)
    {
        $this->basePath = $basePath;
        $this->config   = $config;
        $this->state    = $state;
        $this->newState = new ResultCacheState();
    }

    /**
     * @return ResultCacheConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function accept($relative, $absolute)
    {
        if ($this->config->getStrategy() === 'timestamp') {
            $hash = (string)filemtime($absolute);
        } else {
            $hash = md5_file($absolute);
        }
        $filePath = Paths::getRelativePath($this->basePath, $absolute);

        $isStale = $this->state->isFileStale($filePath, $hash);
        $this->newState->updateFileState($filePath, $hash);

        // TODO transfer violations

        return $isStale;
    }
}
