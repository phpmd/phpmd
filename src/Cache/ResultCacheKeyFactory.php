<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\RuleSet;
use PHPMD\Utility\Paths;

class ResultCacheKeyFactory
{
    public function __construct(
        private readonly string $basePath,
        private readonly ?string $baselineFile,
    ) {
    }

    /**
     * @param RuleSet[] $ruleSetList
     */
    public function create(bool $strict, array $ruleSetList): ResultCacheKey
    {
        return new ResultCacheKey(
            $strict,
            $this->getBaselineHash(),
            $this->createRuleHashes($ruleSetList),
            $this->getComposerHashes(),
            PHP_VERSION_ID
        );
    }

    /**
     * Create a hash array with the FQN of the rule, and the sha1 hash of the serialize rule. This will
     * incorporate any settings for the rule that could invalidate the cache.
     *
     * @param RuleSet[] $ruleSetList
     *
     * @return array<string, string>
     */
    private function createRuleHashes(array $ruleSetList): array
    {
        $result = [];
        foreach ($ruleSetList as $ruleSet) {
            foreach ($ruleSet->getRules() as $rule) {
                $result[$rule::class] = hash('sha1', serialize($rule));
            }
        }

        ksort($result);

        return $result;
    }

    private function getBaselineHash(): ?string
    {
        if (!$this->baselineFile || !file_exists($this->baselineFile)) {
            return null;
        }

        return sha1_file($this->baselineFile) ?: null;
    }

    /**
     * @return array<string, string>
     */
    private function getComposerHashes(): array
    {
        // read sha1 hash of composer.json and lock from current base directory
        $result = [];
        foreach (['composer.json', 'composer.lock'] as $file) {
            $filePath = Paths::concat($this->basePath, $file);
            if (file_exists($filePath)) {
                $hash = sha1_file($filePath);
                if ($hash) {
                    $result[$file] = $hash;
                }
            }
        }

        return $result;
    }
}
