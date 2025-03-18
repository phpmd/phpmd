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
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Rule\TraitAware;
use PHPMD\Utility\Strings;

/**
 * This rule class will detect variables, parameters and properties with really
 * long names.
 */
final class LongVariable extends AbstractRule implements ClassAware, FunctionAware, MethodAware, TraitAware
{
    /**
     * Temporary cache of configured prefixes to subtract
     *
     * @var array<int, string>
     */
    private array $subtractPrefixes;

    /**
     * Temporary cache of configured suffixes to subtract
     *
     * @var array<int, string>
     */
    private array $subtractSuffixes;

    /**
     * Temporary map holding variables that were already processed in the
     * current context.
     *
     * @var array<string, bool>
     */
    private array $processedVariables = [];

    /**
     * Extracts all variable and variable declarator nodes from the given node
     * and checks the variable name length against the configured maximum
     * length.
     */
    public function apply(AbstractNode $node): void
    {
        $this->resetProcessed();

        if ($node->getType() === 'class') {
            $fields = $node->findChildrenOfType(ASTFieldDeclaration::class);
            foreach ($fields as $field) {
                $declarators = $field->findChildrenOfType(ASTVariableDeclarator::class);
                foreach ($declarators as $declarator) {
                    $this->checkNodeImage($declarator);
                }
            }
            $this->resetProcessed();

            return;
        }
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
     * Checks if the variable name of the given node is smaller/equal to the
     * configured threshold.
     *
     * @param AbstractNode<ASTNode> $node
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function checkNodeImage(AbstractNode $node): void
    {
        if ($this->isNotProcessed($node)) {
            $this->addProcessed($node);
            $this->checkMaximumLength($node);
        }
    }

    /**
     * Template method that performs the real node image check.
     *
     * @param AbstractNode<ASTNode> $node
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @SuppressWarnings(LongVariable)
     */
    private function checkMaximumLength(AbstractNode $node): void
    {
        $threshold = $this->getIntProperty('maximum');
        $variableName = $node->getImage();
        $lengthWithoutDollarSign = Strings::lengthWithoutPrefixesAndSuffixes(
            \ltrim($variableName, '$'),
            $this->getSubtractPrefixList(),
            $this->getSubtractSuffixList()
        );
        if ($lengthWithoutDollarSign <= $threshold) {
            return;
        }
        if ($this->isNameAllowedInContext($node)) {
            return;
        }
        $this->addViolation($node, [$variableName, (string) $threshold]);
    }

    /**
     * Checks if a short name is acceptable in the current context. For the
     * moment the only context is a static member.
     *
     * @param AbstractNode<ASTNode> $node
     */
    private function isNameAllowedInContext(AbstractNode $node): bool
    {
        return $node->getParentOfType(ASTMemberPrimaryPrefix::class) !== null;
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

    /**
     * Gets array of suffixes from property
     *
     * @return array<int, string>
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    private function getSubtractPrefixList(): array
    {
        $this->subtractPrefixes ??= Strings::splitToList($this->getStringProperty('subtract-prefixes', ''), ',');

        return $this->subtractPrefixes;
    }

    /**
     * Gets array of suffixes from property
     *
     * @return array<int, string>
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    private function getSubtractSuffixList(): array
    {
        $this->subtractSuffixes ??= Strings::splitToList($this->getStringProperty('subtract-suffixes', ''));

        return $this->subtractSuffixes;
    }
}
