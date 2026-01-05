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
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Utility\ExceptionsList;

/**
 * This rule class will detect methods and functions with very short names.
 */
final class ShortMethodName extends AbstractRule implements FunctionAware, MethodAware
{
    /** Temporary cache of configured exceptions. */
    private ExceptionsList $exceptions;

    /**
     * Extracts all variable and variable declarator nodes from the given node
     * and checks the variable name length against the configured minimum
     * length.
     */
    public function apply(AbstractNode $node): void
    {
        $threshold = $this->getIntProperty('minimum');
        $name = $node->getName();

        if (!$name) {
            return;
        }

        if ($threshold <= strlen($name)) {
            return;
        }

        if ($this->getExceptionsList()->contains($name)) {
            return;
        }

        $this->addViolation(
            $node,
            [
                (string) $node->getParentName(),
                $node->getName(),
                (string) $threshold,
            ]
        );
    }

    /**
     * Gets array of exceptions from property
     */
    private function getExceptionsList(): ExceptionsList
    {
        $this->exceptions ??= new ExceptionsList($this);

        return $this->exceptions;
    }
}
