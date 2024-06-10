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
        if (!file_exists($filePath)) {
            return null;
        }

        $resultCache = require $filePath;
        if (!isset($resultCache['state'], $resultCache['key'])) {
            return null;
        }

        $cacheKey = $this->createCacheKey($resultCache['key']);
        if ($cacheKey === null) {
            return null;
        }

        return new ResultCacheState($cacheKey, $resultCache['state']);
    }

    /**
     * @param array<string, mixed> $data
     * @return ResultCacheKey|null
     */
    private function createCacheKey(array $data)
    {
        if (
            !array_key_exists('strict', $data) ||
            !array_key_exists('baselineHash', $data) ||
            !array_key_exists('rules', $data) ||
            !array_key_exists('composer', $data) ||
            !array_key_exists('phpVersion', $data)
        ) {
            return null;
        }

        assert(is_bool($data['strict']));
        assert(is_string($data['baselineHash']) || $data['baselineHash'] === null);
        assert(is_array($data['rules']));
        assert(is_array($data['composer']));
        assert(is_int($data['phpVersion']));

        return new ResultCacheKey(
            $data['strict'],
            $data['baselineHash'],
            $data['rules'],
            $data['composer'],
            $data['phpVersion']
        );
    }
}
