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

    /** @var array<string, bool> */
    private $fileIsModified = array();

    /**
     * @param string            $basePath
     * @param ResultCacheConfig $config
     * @param ResultCacheState  $state
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
     * A hook to allow filtering out certain files from inspection by pdepend.
     * @inheritDoc
     * @return bool `true` will inspect the file, when `false` the file will be filtered out.
     */
    public function accept($relative, $absolute)
    {
        $filePath = Paths::getRelativePath($this->basePath, $absolute);

        // Seemingly Iterator::accept is invoked more than once for the same file. Cache results for performance.
        if (isset($this->fileIsModified[$filePath])) {
            return $this->fileIsModified[$filePath];
        }

        // Determine file hash. Either `timestamp` or `content`
        if ($this->config->getStrategy() === 'timestamp') {
            $hash = (string)filemtime($absolute);
        } else {
            $hash = md5_file($absolute);
        }

        // Determine if file was modified since last analyses
        $isModified = $this->state->isFileModified($filePath, $hash);

        if ($isModified === false) {
            // File was not modified, transfer previous violations
            $this->newState->setViolations($filePath, $this->state->getViolations($filePath));
        } else {
            // File was modified, set state
            $this->newState->setFileState($filePath, $hash);
        }

        return $this->fileIsModified[$filePath] = $isModified;
    }
}
