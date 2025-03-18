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

namespace PHPMD\Rule\Design;

use InvalidArgumentException;
use PDepend\Source\AST\ASTDoWhileStatement;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTForStatement;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTStatement;
use PDepend\Source\AST\ASTWhileStatement;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ClassNode;
use PHPMD\Node\EnumNode;
use PHPMD\Node\TraitNode;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\EnumAware;
use PHPMD\Rule\TraitAware;
use RuntimeException;

/**
 * Count In Loop Expression Rule
 *
 * Performs a scan to check if loops use
 * count() or sizeof() in expressions.
 *
 * Checks for:
 * - for() loops
 * - while() loops
 * - do-while() loops
 *
 * @author Kamil Szymanski <kamilszymanski@gmail.com>
 * @SuppressWarnings(CouplingBetweenObjects)
 */
final class CountInLoopExpression extends AbstractRule implements ClassAware, EnumAware, TraitAware
{
    /**
     * List of functions to search against
     *
     * @var list<string>
     */
    private array $unwantedFunctions = ['count', 'sizeof'];

    /**
     * List of already processed functions
     *
     * @var array<string, bool>
     */
    private array $processedFunctions = [];

    /** Functions in classes tends to be name-spaced */
    private string $currentNamespace = '';

    /**
     * Gets a list of loops in a node and iterates over them
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function apply(AbstractNode $node): void
    {
        if ($node instanceof ClassNode || $node instanceof TraitNode || $node instanceof EnumNode) {
            $this->applyOnClassMethods($node);

            return;
        }

        $this->currentNamespace = $node->getNamespaceName() . '\\';
        $loops = [
            ...$node->findChildrenOfType(ASTForStatement::class),
            ...$node->findChildrenOfType(ASTWhileStatement::class),
            ...$node->findChildrenOfType(ASTDoWhileStatement::class),
        ];

        foreach ($loops as $loop) {
            $this->findViolations($loop);
        }
    }

    /**
     * Scans for expressions and count() or sizeof() functions inside,
     * if found, triggers a violation
     *
     * @param AbstractNode<ASTStatement> $loop Loop statement to look against
     */
    private function findViolations(AbstractNode $loop): void
    {
        foreach ($loop->findChildrenOfType(ASTExpression::class) as $expression) {
            if ($this->isDirectChild($loop, $expression)) {
                continue;
            }

            foreach ($expression->findChildrenOfType(ASTFunctionPostfix::class) as $function) {
                if (!$this->isUnwantedFunction($function)) {
                    continue;
                }

                $hash = $this->getHash($function->getNode());
                if (isset($this->processedFunctions[$hash])) {
                    continue;
                }

                $this->addViolation($loop, [$function->getImage(), $loop->getImage()]);
                $this->processedFunctions[$hash] = true;
            }
        }
    }

    /**
     * Checks whether node in a direct child of the loop
     *
     * @param AbstractNode<ASTStatement> $loop
     * @param AbstractNode<ASTExpression> $expression
     */
    private function isDirectChild(AbstractNode $loop, AbstractNode $expression): bool
    {
        $parent = $expression->getParent();

        return $parent && $this->getHash($parent->getNode()) !== $this->getHash($loop->getNode());
    }

    /**
     * Generates an unique hash for a given node
     *
     * PDepend method getChildrenOfType() iterates trough all children of a node.
     * As one function may be found more than once, we use a hash (which in reality
     * is a clone of the node's metadata) to check, if a given node hasn't
     * already been processed.
     *
     * Example hash:
     * 22:22:10:15:PHPMD\count
     */
    private function getHash(PDependNode $node): string
    {
        return sprintf(
            '%s:%s:%s:%s:%s',
            $node->getStartLine(),
            $node->getEndLine(),
            $node->getStartColumn(),
            $node->getEndColumn(),
            $node->getImage()
        );
    }

    /**
     * Checks the given function against the list of unwanted functions
     *
     * @param AbstractNode<ASTFunctionPostfix> $function
     */
    private function isUnwantedFunction(AbstractNode $function): bool
    {
        $functionName = str_replace($this->currentNamespace, '', $function->getImage());

        return in_array($functionName, $this->unwantedFunctions, true);
    }
}
