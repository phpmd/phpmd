<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;

class ResultCacheStateFactory
{
    /**
     * @param string $filePath
     * @return ResultCacheState|null
     */
    public function fromFile($filePath)
    {
        if (file_exists($filePath) === false) {
            return null;
        }

        $resultCache = require $filePath;
        if (isset($resultCache['state'], $resultCache['key']) === false) {
            return null;
        }

        $cacheKey = $this->createCacheKey($resultCache['key']);
        if ($cacheKey === null) {
            return null;
        }

        return new ResultCacheState($cacheKey, $resultCache['state']);
    }

    /**
     * @return ResultCacheKey|null
     */
    private function createCacheKey(array $data)
    {
        if (isset($data['strict'], $data['rules'], $data['composer'], $data['phpVersion']) === false) {
            return null;
        }

        return new ResultCacheKey($data['strict'], $data['rules'], $data['composer'], $data['phpVersion']);
    }
}
