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

use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTNode;
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Node\AbstractTypeNode;
use PHPMD\Node\Annotations;
use PHPMD\Node\Attributes;
use PHPMD\Node\NodeInfo;
use PHPMD\Node\NodeInfoFactory;
use PHPMD\Rule\CleanCode\StaticAccess;
use PHPMD\Rule\UnusedSuppression;

/**
 * Filters out the violations of suppressed code, keeping track of which
 * {@see SuppressWarnings} attributes were needed, so the ones that were not can
 * be reported by the {@see UnusedSuppression} rule.
 *
 * The rules are applied to suppressed code like any other, and the violations
 * are matched to the suppressions by the lines they are reported on, which
 * works no matter which node a rule was applied to when it found them.
 */
final class Suppressions
{
    /**
     * The suppressing nodes, with their suppressions.
     *
     * @var list<array{
     *     node: AbstractNode<ASTNode>,
     *     annotations: Annotations,
     *     attributes: list<array{rule: ?string, beginLine: int, endLine: int}>,
     * }>
     */
    private array $scopes = [];

    /**
     * The attributes that suppressed a violation, by scope and attribute index.
     *
     * @var array<int, array<int, true>>
     */
    private array $used = [];

    /** The rule to report unused suppressions for, when it is active. */
    private ?UnusedSuppression $rule = null;

    /** Keeps the suppressed violations when true. */
    private bool $strict = false;

    /**
     * Reports the unused suppressions if the UnusedSuppression rule is one of
     * the given rules, and keeps the suppressed violations in strict mode.
     *
     * @param list<RuleSet> $ruleSets
     */
    public function configure(array $ruleSets): void
    {
        foreach ($ruleSets as $ruleSet) {
            $this->strict = $this->strict || $ruleSet->isStrict();

            foreach ($ruleSet->getRules() as $rule) {
                if ($rule instanceof UnusedSuppression) {
                    $this->rule = $rule;
                }
            }
        }
    }

    /**
     * Collects the suppressions of the given node, and of the properties
     * declared in it.
     *
     * @param AbstractNode<ASTNode> $node
     */
    public function collect(AbstractNode $node): void
    {
        $this->addScope($node);

        if (!$node instanceof AbstractTypeNode) {
            return;
        }

        foreach ($node->findChildrenOfType(ASTFieldDeclaration::class) as $field) {
            $this->addScope($field);
        }
    }

    /**
     * Removes the suppressed violations, and adds one for every suppression
     * that was not needed. Then forgets the collected suppressions.
     *
     * Suppressions of the UnusedSuppression rule are resolved last, since they
     * can only be used by suppressing the report of another suppression.
     *
     * @param iterable<RuleViolation> $violations
     * @return list<RuleViolation>
     */
    public function resolve(iterable $violations): array
    {
        $result = [];

        foreach ($violations as $violation) {
            if (!$this->suppress($violation, false) || $this->strict) {
                $result[] = $violation;
            }
        }

        foreach ($this->findUnused(false) as $index => $unused) {
            foreach ($unused as $violation) {
                // The report is on the attribute, just outside the node it belongs to.
                $suppressed = $this->suppressInScope($index, $violation, true);
                $suppressed = $this->suppress($violation, true) || $suppressed;
                if (!$suppressed || $this->strict) {
                    $result[] = $violation;
                }
            }
        }

        foreach ($this->findUnused(true) as $unused) {
            $result = [...$result, ...$unused];
        }
        $this->scopes = [];
        $this->used = [];

        return $result;
    }

    /**
     * @param AbstractNode<ASTNode> $node
     */
    private function addScope(AbstractNode $node): void
    {
        $this->scopes[] = [
            'node' => $node,
            'annotations' => new Annotations($node),
            'attributes' => (new Attributes($node))->getSuppressions(),
        ];
    }

    /**
     * Marks every suppression of the violation as used, and tells if there
     * were any.
     *
     * @param bool $explicitOnly Ignores the suppressions without a rule when true.
     */
    private function suppress(RuleViolation $violation, bool $explicitOnly): bool
    {
        $suppressed = false;

        foreach ($this->scopes as $index => $scope) {
            $node = $scope['node'];

            if (
                $node->getFileName() === $violation->getFileName() &&
                $node->getBeginLine() <= $violation->getBeginLine() &&
                $node->getEndLine() >= $violation->getEndLine() &&
                $this->suppressInScope($index, $violation, $explicitOnly)
            ) {
                $suppressed = true;
            }
        }

        return $suppressed;
    }

    /**
     * Marks the suppressions of the violation in the given scope as used, and
     * tells if there were any.
     *
     * @param bool $explicitOnly Ignores the suppressions without a rule when true.
     */
    private function suppressInScope(int $index, RuleViolation $violation, bool $explicitOnly): bool
    {
        $rule = $violation->getRule();
        $scope = $this->scopes[$index];
        $suppressed = $scope['annotations']->suppresses($rule);

        foreach ($scope['attributes'] as $key => $attribute) {
            if ($attribute['rule'] === $rule::class || (!$explicitOnly && $attribute['rule'] === null)) {
                $this->used[$index][$key] = true;
                $suppressed = true;
            }
        }

        return $suppressed;
    }

    /**
     * Returns a violation for every unused suppression, either those of the
     * UnusedSuppression rule, or the others, by scope.
     *
     * @return array<int, list<RuleViolation>>
     */
    #[SuppressWarnings(StaticAccess::class)]
    private function findUnused(bool $ofUnusedSuppression): array
    {
        if ($this->rule === null) {
            return [];
        }

        $violations = [];

        foreach ($this->scopes as $index => $scope) {
            foreach ($scope['attributes'] as $key => $attribute) {
                if (
                    isset($this->used[$index][$key]) ||
                    ($attribute['rule'] === $this->rule::class) !== $ofUnusedSuppression
                ) {
                    continue;
                }

                $nodeInfo = NodeInfoFactory::fromNode($scope['node']);
                $violations[$index][] = new RuleViolation(
                    $this->rule,
                    new NodeInfo(
                        $nodeInfo->fileName,
                        $nodeInfo->namespaceName,
                        $nodeInfo->className,
                        $nodeInfo->methodName,
                        $nodeInfo->functionName,
                        $attribute['beginLine'],
                        $attribute['endLine'],
                    ),
                    ['message' => $this->rule->getMessage(), 'args' => [$this->describe($attribute['rule'])]],
                );
            }
        }

        return $violations;
    }

    /**
     * Describes the suppression the way it is usually written.
     */
    private function describe(?string $rule): string
    {
        if ($rule === null) {
            return '#[SuppressWarnings]';
        }

        $position = strrpos($rule, '\\');
        $shortName = $position === false ? $rule : substr($rule, $position + 1);

        return "#[SuppressWarnings({$shortName}::class)]";
    }
}
