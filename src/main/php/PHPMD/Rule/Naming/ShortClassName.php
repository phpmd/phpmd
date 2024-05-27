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
use PHPMD\Rule\EnumAware;
use PHPMD\Rule\InterfaceAware;
use PHPMD\Rule\TraitAware;
use PHPMD\Utility\ExceptionsList;

/**
 * This rule will detect classes and interfaces with names that are too short.
 */
final class ShortClassName extends AbstractRule implements ClassAware, EnumAware, InterfaceAware, TraitAware
{
    /** Temporary cache of configured exceptions. Have name as key */
    private ExceptionsList $exceptions;

    /**
     * Check if a class or interface name is below the minimum configured length and emit a rule violation
     */
    public function apply(AbstractNode $node): void
    {
        $threshold = $this->getIntProperty('minimum');
        $classOrInterfaceName = $node->getName();
        if (strlen($classOrInterfaceName) >= $threshold) {
            return;
        }

        if ($this->getExceptionsList()->contains($classOrInterfaceName)) {
            return;
        }

        $this->addViolation($node, [$classOrInterfaceName, (string) $threshold]);
    }

    /**
     * Gets exceptions from property
     */
    private function getExceptionsList(): ExceptionsList
    {
        $this->exceptions ??= new ExceptionsList($this, '\\');

        return $this->exceptions;
    }
}
