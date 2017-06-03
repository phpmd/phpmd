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

/**
 * This rule class will detect variables, parameters and properties with short
 * names.
 */
class ShortVariable extends AbstractRule implements ClassAware, MethodAware, FunctionAware
{
    /**
     * Temporary map holding variables that were already processed in the
     * current context.
     *
     * @var array(string=>boolean)
     */
    private $processedVariables = array();

    /**
     * Extracts all variable and variable declarator nodes from the given node
     * and checks the variable name length against the configured minimum
     * length.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $this->resetProcessed();

        if ($node->getType() === 'class') {
            $fields = $node->findChildrenOfType('FieldDeclaration');
            foreach ($fields as $field) {
                $declarators = $field->findChildrenOfType('VariableDeclarator');
                foreach ($declarators as $declarator) {
                    $this->checkNodeImage($declarator);
                }
            }
        } else {
            $declarators = $node->findChildrenOfType('VariableDeclarator');
            foreach ($declarators as $declarator) {
                $this->checkNodeImage($declarator);
            }

            $variables = $node->findChildrenOfType('Variable');
            foreach ($variables as $variable) {
                $this->checkNodeImage($variable);
            }
        }

        $this->resetProcessed();
    }

    /**
     * Checks if the variable name of the given node is greater/equal to the
     * configured threshold or if the given node is an allowed context.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    protected function checkNodeImage(AbstractNode $node)
    {
        if ($this->isNotProcessed($node)) {
            $this->addProcessed($node);
            $this->checkMinimumLength($node);
        }
    }

    /**
     * Template method that performs the real node image check.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    protected function checkMinimumLength(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('minimum');

        if ($threshold <= strlen($node->getImage()) - 1) {
            return;
        }

        if ($this->isNameAllowedInContext($node)) {
            return;
        }

        $exceptions = $this->getExceptionsList();

        if (in_array(substr($node->getImage(), 1), $exceptions)) {
            return;
        }

        $this->addViolation($node, array($node->getImage(), $threshold));
    }

    /**
     * Gets array of exceptions from property
     *
     * @return array
     */
    private function getExceptionsList()
    {
        try {
            $exceptions = $this->getStringProperty('exceptions');
        } catch (\OutOfBoundsException $e) {
            $exceptions = '';
        }

        return explode(',', $exceptions);
    }

    /**
     * Checks if a short name is acceptable in the current context. For the
     * moment these contexts are the init section of a for-loop and short
     * variable names in catch-statements.
     *
     * @param \PHPMD\AbstractNode $node
     * @return boolean
     */
    private function isNameAllowedInContext(AbstractNode $node)
    {
        return $this->isChildOf($node, 'CatchStatement')
                || $this->isChildOf($node, 'ForInit')
                || $this->isChildOf($node, 'ForeachStatement')
                || $this->isChildOf($node, 'MemberPrimaryPrefix');
    }

    /**
     * Checks if the given node is a direct or indirect child of a node with
     * the given type.
     *
     * @param \PHPMD\AbstractNode $node
     * @param string $type
     * @return boolean
     */
    private function isChildOf(AbstractNode $node, $type)
    {
        $parent = $node->getParent();
        while (is_object($parent)) {
            if ($parent->isInstanceOf($type)) {
                return true;
            }
            $parent = $parent->getParent();
        }
        return false;
    }

    /**
     * Resets the already processed nodes.
     *
     * @return void
     */
    protected function resetProcessed()
    {
        $this->processedVariables = array();
    }

    /**
     * Flags the given node as already processed.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    protected function addProcessed(AbstractNode $node)
    {
        $this->processedVariables[$node->getImage()] = true;
    }

    /**
     * Checks if the given node was already processed.
     *
     * @param \PHPMD\AbstractNode $node
     * @return boolean
     */
    protected function isNotProcessed(AbstractNode $node)
    {
        return !isset($this->processedVariables[$node->getImage()]);
    }
}
