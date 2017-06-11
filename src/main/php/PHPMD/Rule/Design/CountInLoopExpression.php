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

use PDepend\Source\AST\AbstractASTNode;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
use PHPMD\Rule\ClassAware;

/**
 * Count In Loop Expression Rule
 *
 * Performs a scan to check if loops use
 * count() or sizeof() in expressions.
 * Works with:
 * - for() loops
 * - while() loops
 * - do-while() loops
 *
 * @author Kamil Szymanski <kamilszymanski@gmail.com>
 */
class CountInLoopExpression extends AbstractRule implements ClassAware
{
    /**
     * List of functions to search against
     *
     * @var array
     */
    private $violatingFunctions = array('count', 'sizeof');

    /**
     * List of already processed functions
     *
     * @var array
     */
    private $processedFunctions = array();

    /**
     * Functions in classes tends to be name-spaced
     *
     * @var string
     */
    private $namespaceName = '';

    /**
     * Gets a list of loops in node and iterates over them
     *
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $this->namespaceName = $node->getNamespaceName() . '\\';
        $loops = array_merge(
            $node->findChildrenOfType('ForStatement'),
            $node->findChildrenOfType('WhileStatement'),
            $node->findChildrenOfType('DoWhileStatement')
        );

        /** @var AbstractNode $loop */
        foreach ($loops as $loop) {
            $this->findPossibleViolations($loop);
        }
    }

    /**
     * Scans for expressions and count() or sizeof() functions inside.
     * If found, triggers violation
     *
     * @param AbstractNode $loop Loop statement to look against
     */
    private function findPossibleViolations(AbstractNode $loop)
    {
        foreach ($loop->findChildrenOfType('Expression') as $expression) {
            if ($this->isDirectChild($loop, $expression)) {
                continue;
            }

            foreach ($expression->findChildrenOfType('FunctionPostfix') as $function) {
                if (!$this->isViolatingRule($function)) {
                    continue;
                }

                $hash = $this->getHash($function->getNode());
                if (isset($this->processedFunctions[$hash])) {
                    continue;
                }

                $this->addViolation($loop, array($function->getImage(), $loop->getImage()));
                $this->processedFunctions[$hash] = true;
            }
        }
    }

    /**
     * Checks if expression node in a direct child of loop
     *
     * @param AbstractNode $loop
     * @param ASTNode $expression
     * @return bool
     */
    private function isDirectChild(AbstractNode $loop, ASTNode $expression)
    {
        return $this->getHash($expression->getParent()->getNode()) !== $this->getHash($loop->getNode());
    }

    /**
     * Generates unique hash for given node
     *
     * @param AbstractASTNode $node
     * @return string
     */
    private function getHash(AbstractASTNode $node)
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
     * Checks if given function exists in violations array
     *
     * @param ASTNode $function
     * @return bool
     */
    private function isViolatingRule(ASTNode $function)
    {
        $functionName = str_replace($this->namespaceName, '', $function->getImage());

        return in_array($functionName, $this->violatingFunctions);
    }
}
