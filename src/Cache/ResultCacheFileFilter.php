<?php

namespace PHPMD\Cache;

use PDepend\Input\Filter;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Console\OutputInterface;
use PHPMD\Utility\Paths;

class ResultCacheFileFilter implements Filter
{
    private readonly ResultCacheState $newState;

    /** @var array<string, bool> */
    private array $fileIsModified = [];

    public function __construct(
        private readonly OutputInterface $output,
        private readonly string $basePath,
        private readonly ResultCacheStrategy $strategy,
        ResultCacheKey $cacheKey,
        private readonly ?ResultCacheState $state,
    ) {
        $this->newState = new ResultCacheState($cacheKey);
    }

    /**
     * Stage 1: A hook to allow filtering out certain files from inspection by pdepend.
     * @inheritDoc
     * @return bool `true` will inspect the file, when `false` the file will be filtered out.
     */
    public function accept($relative, $absolute): bool
    {
        $filePath = Paths::getRelativePath($this->basePath, $absolute);

        // Seemingly Iterator::accept is invoked more than once for the same file. Cache results for performance.
        if (isset($this->fileIsModified[$filePath])) {
            return $this->fileIsModified[$filePath];
        }

        if ($this->strategy === ResultCacheStrategy::Timestamp) {
            $hash = (string) filemtime($absolute);
        } else {
            $hash = sha1_file($absolute);
        }

        // Determine if file was modified since last analyse
        $isModified = $hash === false || ($this->state?->isFileModified($filePath, $hash) ?? true);

        if ($hash !== false) {
            $this->newState->setFileState($filePath, $hash);
        }
        if (!$isModified && $this->state) {
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

    public function getState(): ResultCacheState
    {
        return $this->newState;
    }
}
