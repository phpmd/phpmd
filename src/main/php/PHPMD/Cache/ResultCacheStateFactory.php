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
        if (array_key_exists('strict', $data) === false ||
            array_key_exists('baselineHash', $data) === false ||
            array_key_exists('rules', $data) === false ||
            array_key_exists('composer', $data) === false ||
            array_key_exists('phpVersion', $data) === false
        ) {
            return null;
        }

        return new ResultCacheKey(
            $data['strict'],
            $data['baselineHash'],
            $data['rules'],
            $data['composer'],
            $data['phpVersion']
        );
    }
}
