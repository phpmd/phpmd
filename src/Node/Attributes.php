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

use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTAttribute;
use PDepend\Source\AST\ASTClassFqnPostfix;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PHPMD\AbstractNode;
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Rule;

final class Attributes
{
    /** @var array<string, true> */
    private array $suppressed = [];

    /**
     * @param AbstractNode<AbstractASTArtifact> $node
     */
    public function __construct(AbstractNode $node)
    {
        foreach ($node->getChildren() as $attributes) {
            if (!$attributes instanceof ASTAttribute) {
                continue;
            }
            foreach ($attributes->getChildren() as $attribute) {
                if (!$attribute instanceof ASTAllocationExpression) {
                    continue;
                }
                $allocation = $attribute->getChildren();
                $class = $allocation[0] ?? null;
                if (!$class || trim($class->getImage(), '\\') !== SuppressWarnings::class) {
                    continue;
                }
                $arguments = $allocation[1] ?? null;
                if ($arguments) {
                    // #[SuppressWarnings()]
                    $arguments = $arguments->getChildren();
                }
                if (!$arguments) {
                    // #[SuppressWarnings]
                    $this->suppressed['+all'] = true;

                    continue;
                }
                $argument = $arguments[0];

                if ($argument instanceof ASTLiteral) {
                    // #[SuppressWarnings('\PHPMD\Rules\UnusedLocalVariable')]
                    $this->suppressed[trim($argument->getImage(), '\\\'""')] = true;

                    continue;
                }
                if (!$argument instanceof ASTMemberPrimaryPrefix || !$argument->isStatic()) {
                    continue;
                }
                $children = $argument->getChildren();
                if (!$children[1] instanceof ASTClassFqnPostfix) {
                    continue;
                }
                $rule = $children[0];
                // #[SuppressWarnings(UnusedLocalVariable::class)]
                $this->suppressed[trim($rule->getImage(), '\\')] = true;
            }
        }
    }

    /**
     * Checks if one of the attributes suppresses the given rule.
     */
    public function suppresses(Rule $rule): bool
    {
        return $this->suppressed['+all'] ?? $this->suppressed[$rule::class] ?? false;
    }
}
