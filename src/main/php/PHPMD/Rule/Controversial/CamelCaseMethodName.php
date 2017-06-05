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

namespace PHPMD\Rule\Controversial;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

/**
 * This rule class detects methods not named in camelCase.
 *
 * @author    Francis Besset <francis.besset@gmail.com>
 * @since     1.1.0
 */
class CamelCaseMethodName extends AbstractRule implements MethodAware
{
    protected $ignoredMethods = array(
        '__construct',
        '__destruct',
        '__set',
        '__get',
        '__call',
        '__callStatic',
        '__isset',
        '__unset',
        '__sleep',
        '__wakeup',
        '__toString',
        '__invoke',
        '__set_state',
        '__clone',
        '__debugInfo',
    );

    /**
     * This method checks if a method is not named in camelCase
     * and emits a rule violation.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $methodName = $node->getName();
        if (!in_array($methodName, $this->ignoredMethods)) {
            if (!$this->isValid($methodName)) {
                $this->addViolation(
                    $node,
                    array(
                        $methodName,
                    )
                );
            }
        }
    }

    private function isValid($methodName)
    {
        if ($this->getBooleanProperty('allow-underscore-test') && strpos($methodName, 'test') === 0) {
            return preg_match('/^test[a-zA-Z0-9]*([_][a-z][a-zA-Z0-9]*)?$/', $methodName);
        }

        if ($this->getBooleanProperty('allow-underscore')) {
            return preg_match('/^[_]?[a-z][a-zA-Z0-9]*$/', $methodName);
        }

        return preg_match('/^[a-z][a-zA-Z0-9]*$/', $methodName);
    }
}
