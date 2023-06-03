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
     * @return array
     */
    public function getViolations($filePath)
    {
        if (isset($this->state['files'][$filePath]['violations']) === false) {
            return array();
        }

        return $this->state['files'][$filePath]['violations'];
    }

    /**
     * @param string $filePath
     */
    public function setViolations($filePath, array $violations)
    {
        $this->state['files'][$filePath]['violations'] = $violations;
    }

    /**
     * @param string $filePath
     * @param string $hash
     * @return bool
     */
    public function isFileModified($filePath, $hash)
    {
        if (isset($this->state['files'][$filePath]) === false) {
            return true;
        }

        return $this->state['files'][$filePath]['hash'] !== $hash;
    }

    /**
     * @param string $filePath
     * @param string $hash
     */
    public function setFileState($filePath, $hash)
    {
        return $this->state['files'][$filePath]['hash'] = $hash;
    }
}
