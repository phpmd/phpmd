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
     * @param array{files?: array<string, array{hash: string, violations?: list<array{
     *  metric: mixed,
     *  namespaceName: ?string,
     *  className: ?string,
     *  methodName: ?string,
     *  functionName: ?string,
     *  description: string,
     *  beginLine: int,
     *  endLine: int,
     *  rule: string,
     *  args: ?array<int, string>
     * }>}>} $state
     */
    public function __construct(
        private readonly ResultCacheKey $cacheKey,
        private array $state = [],
    ) {
    }

    public function getCacheKey(): ResultCacheKey
    {
        return $this->cacheKey;
    }

    /**
     * @return list<array{
     *  metric: mixed,
     *  namespaceName: ?string,
     *  className: ?string,
     *  methodName: ?string,
     *  functionName: ?string,
     *  description: string,
     *  beginLine: int,
     *  endLine: int,
     *  rule: string,
     *  args: ?array<int, string>
     * }>
     */
    public function getViolations(string $filePath): array
    {
        if (!isset($this->state['files'][$filePath]['violations'])) {
            return [];
        }

        return $this->state['files'][$filePath]['violations'];
    }

    /**
     * @param list<array{
     *  metric: mixed,
     *  namespaceName: ?string,
     *  className: ?string,
     *  methodName: ?string,
     *  functionName: ?string,
     *  description: string,
     *  beginLine: int,
     *  endLine: int,
     *  rule: string,
     *  args: ?array<int, string>
     * }> $violations
     */
    public function setViolations(string $filePath, array $violations): void
    {
        $this->state['files'][$filePath]['violations'] = $violations;
    }

    public function addRuleViolation(string $filePath, RuleViolation $violation): void
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
     * @param list<RuleSet> $ruleSetList
     * @return list<RuleViolation>
     * @throws OutOfBoundsException
     */
    public function getRuleViolations(string $basePath, array $ruleSetList): array
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

    public function isFileModified(string $filePath, string $hash): bool
    {
        if (!isset($this->state['files'][$filePath]['hash'])) {
            return true;
        }

        return $this->state['files'][$filePath]['hash'] !== $hash;
    }

    public function setFileState(string $filePath, string $hash): void
    {
        $this->state['files'][$filePath]['hash'] = $hash;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->cacheKey->toArray(),
            'state' => $this->state,
        ];
    }

    /**
     * @param RuleSet[] $ruleSetList
     * @throws OutOfBoundsException
     */
    private static function findRuleIn(string $ruleClassName, array $ruleSetList): Rule
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
