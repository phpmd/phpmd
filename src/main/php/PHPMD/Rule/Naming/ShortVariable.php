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

namespace PHPMD\Rule\Naming;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Rule\TraitAware;
use PHPMD\RuleProperty\Option;
use PHPMD\RuleProperty\Threshold;
use PHPMD\RuleProperty\Matcher;
use PHPMD\RuleProperty\MatchList;

/**
 * This rule class will detect variables, parameters and properties with short
 * names.
 */
class ShortVariable extends AbstractRule implements ClassAware, MethodAware, FunctionAware, TraitAware
{
    #[Threshold(['threshold', 'minimum'])]
    public int $threshold;

    /** @SuppressWarnings(LongVariable) */
    #[Option]
    public bool $allowShortVariablesInLoop = true;

    #[MatchList]
    public Matcher $exceptions;

    /**
     * Temporary map holding variables that were already processed in the
     * current context.
     *
     * @var array<string, bool>
     */
    protected array $processedVariables = [];

    /**
     * Extracts all variable and variable declarator nodes from the given node
     *
     * Checks the variable name length against the configured minimum
     * length.
     */
    public function apply(AbstractNode $node): void
    {
        $this->resetProcessed();

        if ($node->getType() === 'class') {
            $this->applyClass($node);

            return;
        }

        $this->applyNonClass($node);
    }

    /**
     * Extracts all variable and variable declarator nodes from the given class node
     *
     * Checks the variable name length against the configured minimum
     * length.
     */
    protected function applyClass(AbstractNode $node): void
    {
        $fields = $node->findChildrenOfType('FieldDeclaration');
        foreach ($fields as $field) {
            $declarators = $field->findChildrenOfType('VariableDeclarator');
            foreach ($declarators as $declarator) {
                $this->checkNodeImage($declarator);
            }
        }
        $this->resetProcessed();
    }

    /**
     * Extracts all variable and variable declarator nodes from the given non-class node
     *
     * Checks the variable name length against the configured minimum
     * length.
     */
    protected function applyNonClass(AbstractNode $node): void
    {
        $declarators = $node->findChildrenOfType('VariableDeclarator');
        foreach ($declarators as $declarator) {
            $this->checkNodeImage($declarator);
        }

        $variables = $node->findChildrenOfTypeVariable();
        foreach ($variables as $variable) {
            $this->checkNodeImage($variable);
        }
        $this->resetProcessed();
    }

    /**
     * Checks if the variable name of the given node is greater/equal to the
     * configured threshold or if the given node is an allowed context.
     */
    protected function checkNodeImage(AbstractNode $node): void
    {
        if ($this->isNotProcessed($node)) {
            $this->addProcessed($node);
            $this->checkMinimumLength($node);
        }
    }

    /**
     * Template method that performs the real node image check.
     */
    protected function checkMinimumLength(AbstractNode $node): void
    {
        if ($this->threshold <= \strlen($node->getImage()) - 1) {
            return;
        }

        if ($this->isNameAllowedInContext($node)) {
            return;
        }

        if ($this->exceptions->contains(substr($node->getImage(), 1))) {
            return;
        }

        $this->addViolation($node, [$node->getImage(), $this->threshold]);
    }

    /**
     * Checks if a short name is acceptable in the current context. For the
     * moment these contexts are the init section of a for-loop and short
     * variable names in catch-statements.
     */
    protected function isNameAllowedInContext(AbstractNode $node): bool
    {
        $parent = $node->getParent();

        if ($parent && $parent->isInstanceOf('ForeachStatement')) {
            return $this->isInitializedInLoop($node);
        }

        return $this->isChildOf($node, 'CatchStatement')
            || $this->isChildOf($node, 'ForInit')
            || $this->isChildOf($node, 'MemberPrimaryPrefix');
    }

    /**
     * Checks if a short name is initialized within a foreach loop statement.
     */
    protected function isInitializedInLoop(AbstractNode $node): bool
    {
        if (!$this->allowShortVariablesInLoop) {
            return false;
        }

        $exceptionVariables = [];

        $parentForeaches = $this->getParentsOfType($node, 'ForeachStatement');
        foreach ($parentForeaches as $foreach) {
            foreach ($foreach->getChildren() as $foreachChild) {
                $exceptionVariables[] = $foreachChild->getImage();
            }
        }

        $exceptionVariables = array_filter(array_unique($exceptionVariables));

        return in_array($node->getImage(), $exceptionVariables, true);
    }

    /**
     * Returns an array of parent nodes of the specified type
     */
    protected function getParentsOfType(AbstractNode $node, string $type): array
    {
        $parents = [];

        $parent = $node->getParent();

        while (\is_object($parent)) {
            if ($parent->isInstanceOf($type)) {
                $parents[] = $parent;
            }
            $parent = $parent->getParent();
        }

        return $parents;
    }

    /**
     * Checks if the given node is a direct or indirect child of a node with
     * the given type.
     */
    protected function isChildOf(AbstractNode $node, string $type): bool
    {
        $parent = $node->getParent();
        while (\is_object($parent)) {
            if ($parent->isInstanceOf($type)) {
                return true;
            }
            $parent = $parent->getParent();
        }

        return false;
    }

    /**
     * Resets the already processed nodes.
     */
    protected function resetProcessed(): void
    {
        $this->processedVariables = [];
    }

    /**
     * Flags the given node as already processed.
     */
    protected function addProcessed(AbstractNode $node): void
    {
        $this->processedVariables[$node->getImage()] = true;
    }

    /**
     * Checks if the given node was already processed.
     */
    protected function isNotProcessed(AbstractNode $node): bool
    {
        return !isset($this->processedVariables[$node->getImage()]);
    }
}
