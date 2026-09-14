<?php

namespace PHPMD\Cache;

use PDepend\Input\Filter;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Utility\Paths;
use Symfony\Component\Console\Output\OutputInterface;

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
     * Stage 1: Accepted all files so that the declared types stay resolvable for the rules.
     * This leaves Pdepend to handle caching for file parsing.
     * @inheritDoc
     */
    public function accept($relative, $absolute): bool
    {
        $this->isFileModified($absolute);

        return true;
    }

    /**
     * Whether the file changed since the last analysis.
     */
    public function isFileModified(string $absolute): bool
    {
        $filePath = Paths::getRelativePath($this->basePath, $absolute);

        // Seemingly Iterator::accept is invoked more than once for the same file. Cache results for performance.
        if (isset($this->fileIsModified[$filePath])) {
            return $this->fileIsModified[$filePath];
        }

        $hash = $this->strategy === ResultCacheStrategy::Timestamp
            ? (string) filemtime($absolute)
            : sha1_file($absolute);

        // Determine if file was modified since last analyse
        $isModified = $hash === false || ($this->state?->isFileModified($filePath, $hash) ?? true);

        if ($hash !== false) {
            $this->newState->setFileState($filePath, $hash);
        }
        if (!$isModified && $this->state) {
            // File was not modified, transfer previous violations.
            // Files that failed to parse are never cached, so that it reports them afresh on each run.
            $this->newState->setViolations($filePath, $this->state->getViolations($filePath));
        }

        $this->output->writeln(
            'Cache: ' . ($isModified ? 'MISS' : 'HIT') . ' for file ' . $filePath . '.',
            OutputInterface::VERBOSITY_DEBUG
        );

        return $this->fileIsModified[$filePath] = $isModified;
    }

    public function getState(): ResultCacheState
    {
        return $this->newState;
    }
}
