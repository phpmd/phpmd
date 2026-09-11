<?php

/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD;

use PHPMD\Node\NodeInfo;
use RuntimeException;

/**
 * Carries the violations a forked process found back to the process that started it.
 */
final class ViolationTransfer
{
    /**
     * @param list<RuleSet> $ruleSets The rule sets a reported rule is looked up in.
     */
    public function __construct(
        private readonly Report $report,
        private readonly array $ruleSets,
    ) {
    }

    /**
     * Writes what the report holds to $target.
     *
     * @param resource $target
     */
    public function export($target): void
    {
        $violations = [];

        foreach ($this->report->getRuleViolations() as $violation) {
            $violations[] = [
                'rule' => $violation->getRule()::class,
                'fileName' => $violation->getFileName(),
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

        fwrite($target, serialize(['violations' => $violations]));
    }

    /**
     * Writes an error in place of the violations, for a process that did not
     * get to apply all of its rules.
     *
     * @param resource $target
     */
    public function exportError($target, string $message): void
    {
        fwrite($target, serialize(['error' => $message]));
    }

    /**
     * @param resource $source
     * @throws RuntimeException
     */
    public function import($source): void
    {
        rewind($source);
        $reported = (string) stream_get_contents($source);
        $result = $reported === '' ? null : unserialize($reported);

        if (!is_array($result)) {
            throw new RuntimeException('A process applying the rules ended without reporting what it found.');
        }

        $error = $result['error'] ?? null;
        if (is_string($error)) {
            $this->report->addError(new ProcessingError($error));

            return;
        }

        $violations = $result['violations'] ?? null;
        if (!is_array($violations)) {
            throw new RuntimeException('A process applying the rules reported an unreadable result.');
        }

        foreach ($violations as $violation) {
            if (!is_array($violation)) {
                throw new RuntimeException('A process applying the rules reported an unreadable violation.');
            }

            $this->report->addRuleViolation($this->readViolation($violation));
        }
    }

    /**
     * @param array<mixed> $violation
     * @throws RuntimeException
     */
    private function readViolation(array $violation): RuleViolation
    {
        $description = $violation['description'] ?? null;
        $args = $this->readArgs($violation);
        $metric = $violation['metric'] ?? null;

        if (!is_string($description)) {
            throw new RuntimeException('A process applying the rules reported a violation without a message.');
        }

        if ($metric !== null && !is_float($metric) && !is_int($metric) && !is_numeric($metric)) {
            throw new RuntimeException('A process applying the rules reported a violation with a broken metric.');
        }

        $nodeInfo = new NodeInfo(
            $this->readText($violation, 'fileName'),
            $this->readText($violation, 'namespaceName'),
            $this->readText($violation, 'className'),
            $this->readText($violation, 'methodName'),
            $this->readText($violation, 'functionName'),
            $this->readLine($violation, 'beginLine'),
            $this->readLine($violation, 'endLine'),
        );

        return new RuleViolation(
            $this->findRule((string) $this->readText($violation, 'rule')),
            $nodeInfo,
            $args === null ? $description : ['args' => $args, 'message' => $description],
            $metric,
        );
    }

    /**
     * The arguments a violation was described with, or null when it carries only a message.
     *
     * @param array<mixed> $violation
     * @return list<string>|null
     * @throws RuntimeException
     */
    private function readArgs(array $violation): ?array
    {
        $args = $violation['args'] ?? null;

        if ($args === null) {
            return null;
        }

        if (!is_array($args)) {
            throw new RuntimeException('A process applying the rules reported broken violation arguments.');
        }

        $values = [];

        foreach ($args as $arg) {
            if (!is_scalar($arg)) {
                throw new RuntimeException('A process applying the rules reported a broken violation argument.');
            }

            $values[] = (string) $arg;
        }

        return $values;
    }

    /**
     * @param array<mixed> $violation
     * @throws RuntimeException
     */
    private function readText(array $violation, string $field): ?string
    {
        $value = $violation[$field] ?? null;

        if ($value !== null && !is_string($value)) {
            throw new RuntimeException(sprintf('A process applying the rules reported a broken "%s".', $field));
        }

        return $value;
    }

    /**
     * @param array<mixed> $violation
     * @throws RuntimeException
     */
    private function readLine(array $violation, string $field): int
    {
        $value = $violation[$field] ?? null;

        if (!is_int($value)) {
            throw new RuntimeException(sprintf('A process applying the rules reported a broken "%s".', $field));
        }

        return $value;
    }

    /**
     * @throws RuntimeException
     */
    private function findRule(string $ruleClassName): Rule
    {
        foreach ($this->ruleSets as $ruleSet) {
            foreach ($ruleSet->getRules() as $rule) {
                if ($rule::class === $ruleClassName) {
                    return $rule;
                }
            }
        }

        throw new RuntimeException(sprintf('Unknown rule "%s" reported.', $ruleClassName));
    }
}
