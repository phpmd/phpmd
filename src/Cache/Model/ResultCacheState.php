<?php

namespace PHPMD\Cache\Model;

use PHPMD\Node\NodeInfo;
use PHPMD\Rule;
use PHPMD\RuleSet;
use PHPMD\RuleViolation;
use PHPMD\Utility\Paths;

class ResultCacheState
{
    /** @var ResultCacheKey */
    private $cacheKey;

    /** @var array{files: array<string, array{hash: string, violations: array}>} */
    private $state;

    /**
     * @param array{files: array<string, array{hash: string, violations: array}>} $state
     */
    public function __construct(ResultCacheKey $cacheKey, $state = array())
    {
        $this->cacheKey = $cacheKey;
        $this->state    = $state;
    }

    /**
     * @return ResultCacheKey
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

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
     */
    public function addRuleViolation($filePath, RuleViolation $violation)
    {
        $this->state['files'][$filePath]['violations'][] = array(
            'rule'          => get_class($violation->getRule()),
            'namespaceName' => $violation->getNamespaceName(),
            'className'     => $violation->getClassName(),
            'methodName'    => $violation->getMethodName(),
            'functionName'  => $violation->getFunctionName(),
            'beginLine'     => $violation->getBeginLine(),
            'endLine'       => $violation->getEndLine(),
            'description'   => $violation->getDescription(),
            'args'          => $violation->getArgs(),
            'metric'        => $violation->getMetric()
        );
    }

    /**
     * @param string    $basePath
     * @param RuleSet[] $ruleSetList
     */
    public function getRuleViolations($basePath, array $ruleSetList)
    {
        if (isset($this->state['files']) === false) {
            return array();
        }

        $ruleViolations = array();

        foreach ($this->state['files'] as $filePath => $violations) {
            if (isset($violations['violations']) === false) {
                continue;
            }
            foreach ($violations['violations'] as $violation) {
                $rule     = self::findRuleIn($violation['rule'], $ruleSetList);
                $nodeInfo = new NodeInfo(
                    Paths::concat($basePath, $filePath),
                    $violation['namespaceName'],
                    $violation['className'],
                    $violation['methodName'],
                    $violation['functionName'],
                    $violation['beginLine'],
                    $violation['endLine']
                );

                if ($violation['args'] === null) {
                    $violationMessage = $violation['description'];
                } else {
                    $violationMessage = array('args' => $violation['args'], 'message' => $violation['description']);
                }
                $ruleViolations[] = new RuleViolation($rule, $nodeInfo, $violationMessage, $violation['metric']);
            }
        }

        return $ruleViolations;
    }

    /**
     * @param string $filePath
     * @param string $hash
     * @return bool
     */
    public function isFileModified($filePath, $hash)
    {
        if (isset($this->state['files'][$filePath]['hash']) === false) {
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

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'key'   => $this->cacheKey->toArray(),
            'state' => $this->state
        );
    }

    /**
     * @param string    $ruleClassName
     * @param RuleSet[] $ruleSetList
     * @return Rule|null
     */
    private static function findRuleIn($ruleClassName, array $ruleSetList)
    {
        foreach ($ruleSetList as $ruleSet) {
            foreach ($ruleSet->getRules() as $rule) {
                if (get_class($rule) === $ruleClassName) {
                    return $rule;
                }
            }
        }

        return null;
    }
}
