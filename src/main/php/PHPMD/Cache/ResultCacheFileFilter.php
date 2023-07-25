<?php

namespace PHPMD\Cache;

use PDepend\Input\Filter;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Console\OutputInterface;
use PHPMD\Utility\Paths;

class ResultCacheFileFilter implements Filter
{
    /** @var string */
    private $strategy;

    /** @var ResultCacheState|null */
    private $state;

    /** @var ResultCacheState */
    private $newState;

    /** @var string */
    private $basePath;

    /** @var array<string, bool> */
    private $fileIsModified = array();

    /** @var OutputInterface */
    private $output;


    /**
     * @param string                $basePath
     * @param string                $strategy
     * @param ResultCacheState|null $state
     */
    public function __construct(OutputInterface $output, $basePath, $strategy, ResultCacheKey $cacheKey, $state)
    {
        $this->output   = $output;
        $this->basePath = $basePath;
        $this->strategy = $strategy;
        $this->state    = $state;
        $this->newState = new ResultCacheState($cacheKey);
    }

    /**
     * Stage 1: A hook to allow filtering out certain files from inspection by pdepend.
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
        if ($this->strategy === 'timestamp') {
            $hash = (string)filemtime($absolute);
        } else {
            $hash = sha1_file($absolute);
        }

        // Determine if file was modified since last analyse
        if ($this->state === null) {
            $isModified = true;
        } else {
            $isModified = $this->state->isFileModified($filePath, $hash);
        }

        $this->newState->setFileState($filePath, $hash);
        if ($isModified === false) {
            // File was not modified, transfer previous violations
            $this->newState->setViolations($filePath, $this->state->getViolations($filePath));
        }

        if ($isModified) {
            $this->output->writeln(
                'Cache: MISS for file ' . $filePath . '.',
                OutputInterface::VERBOSITY_DEBUG
            );
        } else {
            $this->output->writeln(
                'Cache: HIT for file ' . $filePath . '.',
                OutputInterface::VERBOSITY_DEBUG
            );
        }

        return $this->fileIsModified[$filePath] = $isModified;
    }

    /**
     * @return ResultCacheState
     */
    public function getState()
    {
        return $this->newState;
    }
}
