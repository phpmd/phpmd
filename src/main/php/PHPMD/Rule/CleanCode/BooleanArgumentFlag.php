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

use PDepend\Source\AST\ASTValue;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Check for a boolean flag in the method/function signature.
 *
 * Boolean flags are signs for single responsibility principle violations.
 */
class BooleanArgumentFlag extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * This method checks if a method/function has boolean flag arguments and warns about them.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('FormalParameter') as $param) {
            $declarator = $param->getFirstChildOfType('VariableDeclarator');
            $value = $declarator->getValue();

            if (false === $this->isBooleanValue($value)) {
                continue;
            }

            $this->addViolation($param, array($node->getImage(), $declarator->getImage()));
        }
    }

    private function isBooleanValue(ASTValue $value = null)
    {
        return $value && $value->isValueAvailable() && ($value->getValue() === true || $value->getValue() === false);
    }
}
