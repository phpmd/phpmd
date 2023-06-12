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
    public static function fromFile($filePath)
    {
        if (file_exists($filePath) === false) {
            return null;
        }

        $resultCache = require $filePath;
        if (isset($resultCache['state'], $resultCache['key']['rules'], $resultCache['key']['phpVersion']) === false) {
            return null;
        }

        $cacheKey = new ResultCacheKey($resultCache['key']['rules'], $resultCache['key']['phpVersion']);

        return new ResultCacheState($cacheKey, $resultCache['state']);
    }
}
