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

use OutOfBoundsException;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\AbstractCallableNode;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule class checks for excessive long function and method parameter lists.
 */
final class ExcessiveParameterList extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * This method checks the number of arguments for the given function or method
     * node against a configured threshold.
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof AbstractCallableNode) {
            return;
        }

        $threshold = $this->getIntProperty('minimum');
        $count = $node->getParameterCount();
        if ($count < $threshold) {
            return;
        }

        $exceptions = $this->getExceptionsList();
        if (in_array($node->getName(), $exceptions, true)) {
            return;
        }

        $this->addViolation(
            $node,
            [
                $node->getType(),
                $node->getName(),
                (string) $count,
                (string) $threshold,
            ]
        );
    }

    /**
     * Gets array of exceptions from property
     *
     * @return array<string>
     * @throws OutOfBoundsException
     */
    private function getExceptionsList(): array
    {
        $exceptions = $this->getStringProperty('exceptions', '');

        return array_filter(array_map('trim', explode(',', $exceptions)));
    }
}
