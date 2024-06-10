<?php

namespace PHPMD\Cache\Model;

use OutOfBoundsException;
use PHPMD\Node\NodeInfo;
use PHPMD\Rule;
use PHPMD\RuleSet;
use PHPMD\RuleViolation;
use PHPMD\Utility\Paths;

class ResultCacheState
{
    /**
     * @param array{files?: array<string, array{hash: string, violations?: list<array{metric: mixed, namespaceName: ?string, className: ?string, methodName: ?string, functionName: ?string, description: string, beginLine: int, endLine: int, rule: string, args: ?array<int, string>}>}>} $state
     */
    public function __construct(
        private readonly ResultCacheKey $cacheKey,
        private array $state = [],
    ) {
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
     * @return list<array{metric: mixed, namespaceName: ?string, className: ?string, methodName: ?string, functionName: ?string, description: string, beginLine: int, endLine: int, rule: string, args: ?array<int, string>}>
     */
    public function getViolations($filePath)
    {
        if (!isset($this->state['files'][$filePath]['violations'])) {
            return [];
        }

        return $this->state['files'][$filePath]['violations'];
    }

    /**
     * @param string $filePath
     * @param list<array{metric: mixed, namespaceName: ?string, className: ?string, methodName: ?string, functionName: ?string, description: string, beginLine: int, endLine: int, rule: string, args: ?array<int, string>}> $violations
     */
    public function setViolations($filePath, array $violations): void
    {
        $this->state['files'][$filePath]['violations'] = $violations;
    }

    /**
     * @param string $filePath
     */
    public function addRuleViolation($filePath, RuleViolation $violation): void
    {
        $this->state['files'][$filePath]['violations'][] = [
            'rule' => $violation->getRule()::class,
            'namespaceName' => $violation->getNamespaceName(),
            'className' => $violation->getClassName(),
            'methodName' => $violation->getMethodName(),
            'functionName' => $violation->getFunctionName(),
            'beginLine' => $violation->getBeginLine(),
            'endLine' => $violation->getEndLine(),
            'description' => $violation->getDescription(),
            'args' => $violation->getArgs(),
            'metric' => $violation->getMetric(),
        ];
    }

    /**
     * @param string $basePath
     * @param list<RuleSet> $ruleSetList
     * @return list<RuleViolation>
     * @throws OutOfBoundsException
     */
    public function getRuleViolations($basePath, array $ruleSetList)
    {
        if (!isset($this->state['files'])) {
            return [];
        }

        $ruleViolations = [];

        foreach ($this->state['files'] as $filePath => $violations) {
            if (!isset($violations['violations'])) {
                continue;
            }
            foreach ($violations['violations'] as $violation) {
                $rule = self::findRuleIn($violation['rule'], $ruleSetList);
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
                    $violationMessage = ['args' => $violation['args'], 'message' => $violation['description']];
                }
                assert(is_numeric($violation['metric']));
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
        if (!isset($this->state['files'][$filePath]['hash'])) {
            return true;
        }

        return $this->state['files'][$filePath]['hash'] !== $hash;
    }

    /**
     * @param string $filePath
     * @param string $hash
     */
    public function setFileState($filePath, $hash): void
    {
        $this->state['files'][$filePath]['hash'] = $hash;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray()
    {
        return [
            'key' => $this->cacheKey->toArray(),
            'state' => $this->state,
        ];
    }

    /**
     * @param string    $ruleClassName
     * @param RuleSet[] $ruleSetList
     * @return Rule
     * @throws OutOfBoundsException
     */
    private static function findRuleIn($ruleClassName, array $ruleSetList)
    {
        foreach ($ruleSetList as $ruleSet) {
            foreach ($ruleSet->getRules() as $rule) {
                if ($rule::class === $ruleClassName) {
                    return $rule;
                }
            }
        }

        throw new OutOfBoundsException();
    }
}
