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
use PHPMD\Node\ClassNode;
use PHPMD\Rule\ClassAware;

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
 */
class CountInLoopExpression extends AbstractRule implements ClassAware
{
    /**
     * List of functions to search against
     *
     * @var array
     */
    protected $unwantedFunctions = array('count', 'sizeof');

    /**
     * List of already processed functions
     *
     * @var array
     */
    protected $processedFunctions = array();

    /**
     * Functions in classes tends to be name-spaced
     *
     * @var string
     */
    protected $currentNamespace = '';

    /**
     * Gets a list of loops in a node and iterates over them
     *
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        if ($node instanceof ClassNode) {
            return $this->applyOnClassMethods($node);
        }

        $this->currentNamespace = $node->getNamespaceName() . '\\';
        $loops = array_merge(
            $node->findChildrenOfType('ForStatement'),
            $node->findChildrenOfType('WhileStatement'),
            $node->findChildrenOfType('DoWhileStatement')
        );

        /** @var AbstractNode $loop */
        foreach ($loops as $loop) {
            $this->findViolations($loop);
        }
    }

    /**
     * Scans for expressions and count() or sizeof() functions inside,
     * if found, triggers a violation
     *
     * @param AbstractNode $loop Loop statement to look against
     */
    protected function findViolations(AbstractNode $loop)
    {
        foreach ($loop->findChildrenOfType('Expression') as $expression) {
            if ($this->isDirectChild($loop, $expression)) {
                continue;
            }

            foreach ($expression->findChildrenOfType('FunctionPostfix') as $function) {
                if (!$this->isUnwantedFunction($function)) {
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
     * Checks whether node in a direct child of the loop
     *
     * @param AbstractNode $loop
     * @param ASTNode $expression
     * @return bool
     */
    protected function isDirectChild(AbstractNode $loop, ASTNode $expression)
    {
        return $this->getHash($expression->getParent()->getNode()) !== $this->getHash($loop->getNode());
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
     *
     * @param AbstractASTNode $node
     * @return string
     */
    protected function getHash(AbstractASTNode $node)
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
     * @param ASTNode $function
     * @return bool
     */
    protected function isUnwantedFunction(ASTNode $function)
    {
        $functionName = str_replace($this->currentNamespace, '', $function->getImage());

        return in_array($functionName, $this->unwantedFunctions);
    }
}
