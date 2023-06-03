<?php

namespace PHPMD\Cache;

class ResultCacheState
{
    /** @var array{files: array<string, array{hash: string, violations: array}>} */
    private $state = array(
        'files' => array(
            'src/main/php/PHPMD/Baseline/BaselineFileFinder.php' => array(
                'hash' => '678869f0ed211c0926c4e6d9c4b9fd23'
            )
        )
    );

    /**
     * @param string $filePath
     * @param string $hash
     * @return bool
     */
    public function isFileStale($filePath, $hash)
    {
        if (isset($this->state['files'][$filePath]) === false) {
            return false;
        }

        return $this->state['files'][$filePath]['hash'] !== $hash;
    }

    /**
     * @param string $filePath
     * @param string $hash
     */
    public function updateFileState($filePath, $hash)
    {
        return $this->state['files'][$filePath]['hash'] = $hash;
    }
}
