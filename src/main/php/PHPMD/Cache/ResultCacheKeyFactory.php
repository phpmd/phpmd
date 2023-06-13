<?php

namespace PHPMD\Cache;

use PHPMD\AbstractRule;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\RuleSet;

class ResultCacheKeyFactory
{
    /**
     * @param bool $strict
     * @param RuleSet[] $ruleSetList
     */
    public function create($strict, array $ruleSetList)
    {
        return new ResultCacheKey($strict, self::createRuleHashes($ruleSetList), PHP_VERSION_ID);
    }

    /**
     * Create a hash array with the FQN of the rule, and the sha1 hash of the serialize rule. This will
     * incorporate any settings for the rule that could invalidate the cache.
     *
     * @param RuleSet[] $ruleSetList
     *
     * @return array<string, string>
     */
    private static function createRuleHashes(array $ruleSetList)
    {
        $result = array();
        foreach ($ruleSetList as $ruleSet) {
            /** @var AbstractRule $rule */
            foreach ($ruleSet->getRules() as $rule) {
                $result[get_class($rule)] = hash('sha1', serialize($rule));
            }
        }

        ksort($result);

        return $result;
    }
}
