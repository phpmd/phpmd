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

use PDepend\Source\AST\AbstractASTNode;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTNode as PDependASTNode;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
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
class DuplicatedArrayKey extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * Retrieves all arrays from single node and performs comparison logic on it
     *
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('Array') as $arrayNode) {
            /** @var ASTNode $arrayNode */
            $this->checkForDuplicatedArrayKeys($arrayNode);
        }
    }

    /**
     * This method checks if a given function or method contains an array literal
     * with duplicated entries for any key and emits a rule violation if so.
     *
     * @param ASTNode $node Array node.
     * @return void
     */
    protected function checkForDuplicatedArrayKeys(ASTNode $node)
    {
        $keys = array();
        /** @var ASTArrayElement $arrayElement */
        foreach ($node->getChildren() as $index => $arrayElement) {
            $arrayElement = $this->normalizeKey($arrayElement, $index);
            if (null === $arrayElement) {
                // skip everything that can't be resolved easily
                continue;
            }

            $key = $arrayElement->getImage();
            if (isset($keys[$key])) {
                $this->addViolation($node, array($key, $arrayElement->getStartLine()));
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
     * @return AbstractASTNode Key name
     */
    protected function normalizeKey(AbstractASTNode $node, $index)
    {
        $childCount = count($node->getChildren());
        // Skip, if there is no array key, just an array value
        if ($childCount === 1) {
            return null;
        }
        // non-associative - key name equals to its index
        if ($childCount === 0) {
            $node->setImage((string)$index);

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
     *
     * @param PDependASTNode $key
     * @return string
     */
    protected function castStringFromLiteral(PDependASTNode $key)
    {
        $value = $key->getImage();
        switch ($value) {
            case 'false':
                return '0';
            case 'true':
                return '1';
            case 'null':
                return '';
            default:
                return trim($value, '\'""');
        }
    }
}
