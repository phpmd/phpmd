<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;

class ResultCacheStateFactory
{
    public function fromFile(string $filePath): ?ResultCacheState
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $resultCache = require $filePath;
        if (
            !is_array($resultCache)
            || !isset($resultCache['state'], $resultCache['key'])
            || !is_array($resultCache['state'])
            || !is_array($resultCache['key'])
        ) {
            return null;
        }

        /** @var array<string, mixed> */
        $key = $resultCache['key'];
        $cacheKey = $this->createCacheKey($key);
        if ($cacheKey === null) {
            return null;
        }

        /** @var array{files?: array<string, array{hash: string, violations?: list<array{metric: mixed, namespaceName: ?string, className: ?string, methodName: ?string, functionName: ?string, description: string, beginLine: int, endLine: int, rule: string, args: ?array<int, string>}>}>} */
        $state = $resultCache['state'];

        return new ResultCacheState($cacheKey, $state);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createCacheKey(array $data): ?ResultCacheKey
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

        /** @var array<string, string> */
        $rules = $data['rules'];
        assert(is_array($data['composer']));

        /** @var array<string, string> */
        $composer = $data['composer'];
        assert(is_int($data['phpVersion']));

        return new ResultCacheKey(
            $data['strict'],
            $data['baselineHash'],
            $rules,
            $composer,
            $data['phpVersion']
        );
    }
}
