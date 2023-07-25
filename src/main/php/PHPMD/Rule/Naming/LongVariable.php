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
use PHPMD\Utility\Strings;

/**
 * This rule class will detect variables, parameters and properties with really
 * long names.
 */
class LongVariable extends AbstractRule implements ClassAware, MethodAware, FunctionAware, TraitAware
{
    /**
     * Temporary cache of configured prefixes to subtract
     *
     * @var string[]|null
     */
    protected $subtractPrefixes;

    /**
     * Temporary cache of configured suffixes to subtract
     *
     * @var string[]|null
     */
    protected $subtractSuffixes;

    /**
     * Temporary map holding variables that were already processed in the
     * current context.
     *
     * @var array(string=>boolean)
     */
    protected $processedVariables = array();

    /**
     * Extracts all variable and variable declarator nodes from the given node
     * and checks the variable name length against the configured maximum
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
            $this->resetProcessed();

            return;
        }
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
     * Checks if the variable name of the given node is smaller/equal to the
     * configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    protected function checkNodeImage(AbstractNode $node)
    {
        if ($this->isNotProcessed($node)) {
            $this->addProcessed($node);
            $this->checkMaximumLength($node);
        }
    }

    /**
     * Template method that performs the real node image check.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    protected function checkMaximumLength(AbstractNode $node)
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
        $this->addViolation($node, array($variableName, $threshold));
    }

    /**
     * Checks if a short name is acceptable in the current context. For the
     * moment the only context is a static member.
     *
     * @param \PHPMD\AbstractNode $node
     * @return boolean
     */
    protected function isNameAllowedInContext(AbstractNode $node)
    {
        return $this->isChildOf($node, 'MemberPrimaryPrefix');
    }

    /**
     * Checks if the given node is a direct or indirect child of a node with
     * the given type.
     *
     * @param \PHPMD\AbstractNode $node
     * @param string $type
     * @return boolean
     */
    protected function isChildOf(AbstractNode $node, $type)
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

    /**
     * Gets array of suffixes from property
     *
     * @return string[]
     */
    protected function getSubtractPrefixList()
    {
        if ($this->subtractPrefixes === null) {
            $this->subtractPrefixes = Strings::splitToList($this->getStringProperty('subtract-prefixes', ''), ',');
        }

        return $this->subtractPrefixes;
    }

    /**
     * Gets array of suffixes from property
     *
     * @return string[]
     */
    protected function getSubtractSuffixList()
    {
        if ($this->subtractSuffixes === null) {
            $this->subtractSuffixes = Strings::splitToList($this->getStringProperty('subtract-suffixes', ''), ',');
        }

        return $this->subtractSuffixes;
    }
}
