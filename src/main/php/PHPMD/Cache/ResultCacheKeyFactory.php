<?php

namespace PHPMD\Cache;

use PHPMD\AbstractRule;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\RuleSet;
use PHPMD\Utility\Paths;

class ResultCacheKeyFactory
{
    /** @var string */
    private $basePath;
    /** @var string|null */
    private $baselineFile;

    /**
     * @param string      $basePath
     * @param string|null $baselineFile
     */
    public function __construct($basePath, $baselineFile)
    {
        $this->basePath     = $basePath;
        $this->baselineFile = $baselineFile;
    }

    /**
     * @param bool      $strict
     * @param RuleSet[] $ruleSetList
     */
    public function create($strict, array $ruleSetList)
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
    private function createRuleHashes(array $ruleSetList)
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

    /**
     * @return string|null
     */
    private function getBaselineHash()
    {
        if ($this->baselineFile === null || file_exists($this->baselineFile) === false) {
            return null;
        }

        return sha1_file($this->baselineFile);
    }

    /**
     * @return array<string, string>
     */
    private function getComposerHashes()
    {
        // read sha1 hash of composer.json and lock from current base directory
        $result = array();
        foreach (array('composer.json', 'composer.lock') as $file) {
            $filePath = Paths::concat($this->basePath, $file);
            if (file_exists($filePath)) {
                $result[$file] = sha1_file($filePath);
            }
        }

        return $result;
    }
}
