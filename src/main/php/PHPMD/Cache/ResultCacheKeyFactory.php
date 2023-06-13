<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\RuleSet;

class ResultCacheKeyFactory
{
    /**
     * @param RuleSet[] $ruleSetList
     */
    public function create(array $ruleSetList)
    {
        return new ResultCacheKey(self::createRuleHashes($ruleSetList), PHP_VERSION_ID);
    }

    /**
     * @param RuleSet[] $ruleSetList
     *
     * @return array<string, string>
     */
    private static function createRuleHashes(array $ruleSetList)
    {
        $result = array();
        foreach ($ruleSetList as $ruleSet) {
            foreach ($ruleSet->getRules() as $rule) {
                $result[get_class($rule)] = hash('sha1', serialize($rule));
            }
        }

        ksort($result);

        return $result;
    }
}
