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

use OutOfBoundsException;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

/**
 * This rule class detects methods not named in camelCase.
 *
 * @author Francis Besset <francis.besset@gmail.com>
 * @since 1.1.0
 */
final class CamelCaseMethodName extends AbstractRule implements MethodAware
{
    /** @var list<string> */
    private array $ignoredMethods = [
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
        '__serialize',
        '__unserialize',
    ];

    /**
     * This method checks if a method is not named in camelCase
     * and emits a rule violation.
     */
    public function apply(AbstractNode $node): void
    {
        $methodName = $node->getName();
        if (!in_array($methodName, $this->ignoredMethods, true)) {
            if (!$this->isValid($methodName)) {
                $this->addViolation(
                    $node,
                    [
                        $methodName,
                    ]
                );
            }
        }
    }

    /**
     * @throws OutOfBoundsException
     */
    private function isValid(string $methodName): bool
    {
        // disallow any consecutive uppercase letters
        if (
            $this->getBooleanProperty('camelcase-abbreviations', false)
            && preg_match('/[A-Z]{2}/', $methodName) === 1
        ) {
            return false;
        }

        if ($this->getBooleanProperty('allow-underscore-test') && str_starts_with($methodName, 'test')) {
            return preg_match('/^test[a-zA-Z0-9]*(_[a-z][a-zA-Z0-9]*)*$/', $methodName) === 1;
        }

        if ($this->getBooleanProperty('allow-underscore')) {
            return preg_match('/^_?[a-z][a-zA-Z0-9]*$/', $methodName) === 1;
        }

        return preg_match('/^[a-z][a-zA-Z0-9]*$/', $methodName) === 1;
    }
}
