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
use PHPMD\Node\MethodNode;
use PHPMD\Rule\MethodAware;

/**
 * This rule tests that a method which returns a boolean value does not start
 * with <b>get</b> or <b>_get</b> for a getter.
 */
class BooleanGetMethodName extends AbstractRule implements MethodAware
{
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
        /** @var $node MethodNode */
        if ($this->isBooleanGetMethod($node)) {
            $this->addViolation($node, array($node->getImage()));
        }
    }

    /**
     * Tests if the given method matches all criteria to be an invalid
     * boolean get method.
     *
     * @param \PHPMD\Node\MethodNode $node
     * @return boolean
     */
    protected function isBooleanGetMethod(MethodNode $node)
    {
        return $this->isGetterMethodName($node)
            && $this->isReturnTypeBoolean($node)
            && $this->isParameterizedOrIgnored($node);
    }

    /**
     * Tests if the given method starts with <b>get</b> or <b>_get</b>.
     *
     * @param \PHPMD\Node\MethodNode $node
     * @return boolean
     */
    protected function isGetterMethodName(MethodNode $node)
    {
        return (preg_match('(^_?get)i', $node->getImage()) > 0);
    }

    /**
     * Tests if the given method is declared with return type boolean.
     *
     * @param \PHPMD\Node\MethodNode $node
     * @return boolean
     */
    protected function isReturnTypeBoolean(MethodNode $node)
    {
        $comment = $node->getDocComment();

        return (preg_match('(\*\s*@return\s+bool(ean)?\s)i', $comment) > 0);
    }

    /**
     * Tests if the property <b>$checkParameterizedMethods</b> is set to <b>true</b>
     * or has no parameters.
     *
     * @param \PHPMD\Node\MethodNode $node
     * @return boolean
     */
    protected function isParameterizedOrIgnored(MethodNode $node)
    {
        if ($this->getBooleanProperty('checkParameterizedMethods')) {
            return $node->getParameterCount() === 0;
        }

        return true;
    }
}
