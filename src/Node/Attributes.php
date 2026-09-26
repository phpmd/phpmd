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

namespace PHPMD\Node;

use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTAttribute;
use PDepend\Source\AST\ASTClassFqnPostfix;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTSelfReference;
use PHPMD\AbstractNode;
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Rule;
use RuntimeException;

final class Attributes
{
    /** @var array<string, true> */
    private array $suppressed = [];

    /**
     * Every suppression, in the order written. A null rule suppresses all rules.
     *
     * @var list<array{rule: ?string, beginLine: int, endLine: int}>
     */
    private array $suppressions = [];

    /**
     * @param AbstractNode<ASTNode> $node
     */
    public function __construct(AbstractNode $node)
    {
        foreach ($node->getChildren() as $attributes) {
            if (!$attributes instanceof ASTAttribute) {
                continue;
            }
            foreach ($attributes->getChildren() as $attribute) {
                if ($attribute instanceof ASTAllocationExpression) {
                    $this->processAttribute($attribute, $attributes);
                }
            }
        }
    }

    /**
     * @param ASTAttribute $group The #[...] the attribute is written in, which
     *                            unlike the attribute itself knows its lines.
     */
    private function processAttribute(ASTAllocationExpression $attribute, ASTAttribute $group): void
    {
        $allocation = $attribute->getChildren();
        $class = $allocation[0] ?? null;
        $className = $class ? trim($class->getImage(), '\\') : null;
        if ($className !== SuppressWarnings::class) {
            return;
        }
        $rule = $this->getRuleArgument($allocation);
        if ($rule === null) {
            return;
        }

        $this->suppressed[$rule] = true;
        $this->suppressions[] = [
            'rule' => $rule === '+all' ? null : $rule,
            'beginLine' => $group->getStartLine(),
            'endLine' => $group->getEndLine(),
        ];
    }

    /**
     * Returns the rule class the attribute was given, '+all' when it was
     * given none, or null when it could not be read.
     *
     * @param array<ASTNode> $allocation
     */
    private function getRuleArgument(array $allocation): ?string
    {
        $arguments = $allocation[1] ?? null;
        if ($arguments) {
            // #[SuppressWarnings()]
            $arguments = $arguments->getChildren();
        }
        if (!$arguments) {
            // #[SuppressWarnings]
            return '+all';
        }
        $argument = $arguments[0];

        if ($argument instanceof ASTLiteral) {
            // #[SuppressWarnings('\PHPMD\Rules\UnusedLocalVariable')]
            return trim($argument->getImage(), '\\\'""');
        }
        if (!$argument instanceof ASTMemberPrimaryPrefix || !$argument->isStatic()) {
            return null;
        }
        $children = $argument->getChildren();
        if (!$children[1] instanceof ASTClassFqnPostfix) {
            return null;
        }
        if ($children[0] instanceof ASTSelfReference) {
            // #[SuppressWarnings(self::class)]
            try {
                return $children[0]->getType()->getNamespacedName();
            } catch (RuntimeException) {
                return null;
            }
        }

        // #[SuppressWarnings(UnusedLocalVariable::class)]
        return trim($children[0]->getImage(), '\\');
    }

    /**
     * Checks if one of the attributes suppresses the given rule.
     */
    public function suppresses(Rule $rule): bool
    {
        return $this->suppressed['+all'] ?? $this->suppressed[$rule::class] ?? false;
    }

    /**
     * Returns every suppression, in the order written. A null rule suppresses
     * all rules.
     *
     * @return list<array{rule: ?string, beginLine: int, endLine: int}>
     */
    public function getSuppressions(): array
    {
        return $this->suppressions;
    }
}
