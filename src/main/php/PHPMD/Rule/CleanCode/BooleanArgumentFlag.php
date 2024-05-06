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

use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Utility\ExceptionsList;

/**
 * Check for a boolean flag in the method/function signature.
 *
 * Boolean flags are signs for single responsibility principle violations.
 */
class BooleanArgumentFlag extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * Temporary cache of configured exceptions.
     *
     * @var ExceptionsList|null
     */
    protected $exceptions;

    /**
     * This method checks if a method/function has boolean flag arguments and warns about them.
     */
    public function apply(AbstractNode $node): void
    {
        $name = $node->getName();

        if ($name) {
            $ignorePattern = trim($this->getStringProperty('ignorepattern', ''));

            if ($ignorePattern !== '' && preg_match($ignorePattern, $node->getName())) {
                return;
            }
        }

        $currNode = $node->getNode();
        $parent = $currNode->getParent();

        if ($parent &&
            ($parent instanceof AbstractASTClassOrInterface) &&
            ($name = $parent->getName()) &&
            $this->getExceptionsList()->contains($name)
        ) {
            return;
        }

        $this->scanFormalParameters($node);
    }

    protected function isBooleanValue(ASTValue $value = null)
    {
        return $value?->isValueAvailable() && is_bool($value->getValue());
    }

    /**
     * Gets exceptions from property
     *
     * @return ExceptionsList
     */
    protected function getExceptionsList()
    {
        if ($this->exceptions === null) {
            $this->exceptions = new ExceptionsList($this);
        }

        return $this->exceptions;
    }

    private function scanFormalParameters(AbstractNode $node): void
    {
        foreach ($node->findChildrenOfType(ASTFormalParameter::class) as $param) {
            $declarator = $param->getFirstChildOfType(ASTVariableDeclarator::class);
            $value = $declarator->getValue();

            if (!$this->isBooleanValue($value)) {
                continue;
            }

            $this->addViolation($param, [$node->getImage(), $declarator->getImage()]);
        }
    }
}
