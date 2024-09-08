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

use InvalidArgumentException;
use OutOfBoundsException;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTForInit;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Rule\TraitAware;
use PHPMD\Utility\ExceptionsList;

/**
 * This rule class will detect variables, parameters and properties with short
 * names.
 */
final class ShortVariable extends AbstractRule implements ClassAware, FunctionAware, MethodAware, TraitAware
{
    /**
     * Temporary map holding variables that were already processed in the
     * current context.
     *
     * @var array<string, bool>
     */
    private array $processedVariables = [];

    /** Temporary cache of configured exceptions. */
    private ExceptionsList $exceptions;

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
     *
     * @param AbstractNode<ASTNode> $node
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function applyClass(AbstractNode $node): void
    {
        $fields = $node->findChildrenOfType(ASTFieldDeclaration::class);
        foreach ($fields as $field) {
            $declarators = $field->findChildrenOfType(ASTVariableDeclarator::class);
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
     *
     * @param AbstractNode<ASTNode> $node
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function applyNonClass(AbstractNode $node): void
    {
        $declarators = $node->findChildrenOfType(ASTVariableDeclarator::class);
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
     *
     * @param AbstractNode<ASTNode> $node
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function checkNodeImage(AbstractNode $node): void
    {
        if ($this->isNotProcessed($node)) {
            $this->addProcessed($node);
            $this->checkMinimumLength($node);
        }
    }

    /**
     * Template method that performs the real node image check.
     *
     * @param AbstractNode<ASTNode> $node
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function checkMinimumLength(AbstractNode $node): void
    {
        $threshold = $this->getIntProperty('minimum');

        if ($threshold <= strlen($node->getImage()) - 1) {
            return;
        }

        if ($this->isNameAllowedInContext($node)) {
            return;
        }

        $exceptions = $this->getExceptionsList();

        if ($exceptions->contains(substr($node->getImage(), 1))) {
            return;
        }

        $this->addViolation($node, [$node->getImage(), (string) $threshold]);
    }

    /**
     * Gets exceptions from property
     */
    private function getExceptionsList(): ExceptionsList
    {
        $this->exceptions ??= new ExceptionsList($this);

        return $this->exceptions;
    }

    /**
     * Checks if a short name is acceptable in the current context. For the
     * moment these contexts are the init section of a for-loop and short
     * variable names in catch-statements.
     *
     * @param AbstractNode<ASTNode> $node
     * @throws OutOfBoundsException
     */
    private function isNameAllowedInContext(AbstractNode $node): bool
    {
        $parent = $node->getParent();

        if ($parent && $parent->isInstanceOf(ASTForeachStatement::class)) {
            return $this->isInitializedInLoop($node);
        }

        return $this->isChildOf($node, ASTCatchStatement::class)
            || $this->isChildOf($node, ASTForInit::class)
            || $this->isChildOf($node, ASTMemberPrimaryPrefix::class);
    }

    /**
     * Checks if a short name is initialized within a foreach loop statement
     *
     * @param AbstractNode<ASTNode> $node
     * @throws OutOfBoundsException
     */
    private function isInitializedInLoop(AbstractNode $node): bool
    {
        if (!$this->getBooleanProperty('allow-short-variables-in-loop', true)) {
            return false;
        }

        $exceptionVariables = [];

        $parentForeaches = $this->getParentsOfType($node, ASTForeachStatement::class);
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
     *
     * @template T of ASTNode
     * @param AbstractNode<ASTNode> $node
     * @param class-string<T> $type
     * @return list<AbstractNode<T>>
     */
    private function getParentsOfType(AbstractNode $node, $type): array
    {
        $parents = [];

        $parent = $node->getParent();

        while (is_object($parent)) {
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
     *
     * @param AbstractNode<ASTNode> $node
     * @param class-string<ASTNode> $type
     */
    private function isChildOf(AbstractNode $node, $type): bool
    {
        return $node->getParentOfType($type) !== null;
    }

    /**
     * Resets the already processed nodes.
     */
    private function resetProcessed(): void
    {
        $this->processedVariables = [];
    }

    /**
     * Flags the given node as already processed.
     *
     * @param AbstractNode<ASTNode> $node
     */
    private function addProcessed(AbstractNode $node): void
    {
        $this->processedVariables[$node->getImage()] = true;
    }

    /**
     * Checks if the given node was already processed.
     *
     * @param AbstractNode<ASTNode> $node
     */
    private function isNotProcessed(AbstractNode $node): bool
    {
        return !isset($this->processedVariables[$node->getImage()]);
    }
}
