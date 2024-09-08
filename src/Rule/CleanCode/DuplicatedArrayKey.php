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

namespace PHPMD\Rule\CleanCode;

use OutOfBoundsException;
use PDepend\Source\AST\AbstractASTNode;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTNode as PDependASTNode;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Duplicated Array Key Rule
 *
 * This rule detects duplicated array keys.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @author Kamil Szymanaski <kamil.szymanski@gmail.com>
 */
final class DuplicatedArrayKey extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * Retrieves all arrays from single node and performs comparison logic on it
     */
    public function apply(AbstractNode $node): void
    {
        foreach ($node->findChildrenOfType(ASTArray::class) as $arrayNode) {
            $this->checkForDuplicatedArrayKeys($arrayNode);
        }
    }

    /**
     * This method checks if a given function or method contains an array literal
     * with duplicated entries for any key and emits a rule violation if so.
     *
     * @param AbstractNode<ASTArray> $node Array node.
     * @throws OutOfBoundsException
     */
    private function checkForDuplicatedArrayKeys(AbstractNode $node): void
    {
        $keys = [];
        foreach ($node->getChildren() as $index => $arrayElement) {
            if (!$arrayElement instanceof AbstractASTNode) {
                continue;
            }
            $arrayElement = $this->normalizeKey($arrayElement, $index);
            if (null === $arrayElement) {
                // skip everything that can't be resolved easily
                continue;
            }

            $key = $arrayElement->getImage();
            if (isset($keys[$key])) {
                $this->addViolation($node, [$key, (string) $arrayElement->getStartLine()]);

                continue;
            }
            $keys[$key] = $arrayElement;
        }
    }

    /**
     * Changes key name to its string format.
     *
     * To compare keys, we have to cast them to string.
     * Non-associative keys have to use index as its key,
     * while boolean and nulls have to be casted respectively.
     * As current logic doesn't evaluate expressions nor constants,
     * statics, globals, etc. we simply skip them.
     *
     * @param AbstractASTNode $node Array key to evaluate.
     * @param int $index Fallback in case of non-associative arrays
     * @return ?AbstractASTNode Key name
     * @throws OutOfBoundsException
     */
    private function normalizeKey(AbstractASTNode $node, int $index): ?AbstractASTNode
    {
        $childCount = count($node->getChildren());
        // Skip, if there is no array key, just an array value
        if ($childCount === 1) {
            return null;
        }
        // non-associative - key name equals to its index
        if ($childCount === 0) {
            $node->setImage((string) $index);

            return $node;
        }

        $node = $node->getChild(0);
        if (!($node instanceof ASTLiteral)) {
            // skip expressions, method calls, globals and constants
            return null;
        }
        $node->setImage($this->castStringFromLiteral($node));

        return $node;
    }

    /**
     * Cleans string literals and casts boolean and null values as PHP engine does
     */
    private function castStringFromLiteral(PDependASTNode $key): string
    {
        $value = $key->getImage();

        return match ($value) {
            'false' => '0',
            'true' => '1',
            'null' => '',
            default => trim($value, '\'""'),
        };
    }
}
