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
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\EnumAware;
use PHPMD\Rule\InterfaceAware;
use PHPMD\Rule\TraitAware;

/**
 * This rule class detects classes not named in CamelCase.
 *
 * @author Francis Besset <francis.besset@gmail.com>
 * @since 1.1.0
 */
final class CamelCaseClassName extends AbstractRule implements ClassAware, EnumAware, InterfaceAware, TraitAware
{
    /**
     * This method checks if a class is not named in CamelCase
     * and emits a rule violation.
     */
    public function apply(AbstractNode $node): void
    {
        $pattern = '/^[A-Z][a-zA-Z0-9]*$/';
        if ($this->getBooleanProperty('camelcase-abbreviations')) {
            // disallow any consecutive uppercase letters
            $pattern = '/^([A-Z][a-z0-9]+)*$/';
        }

        if (!preg_match($pattern, $node->getName())) {
            $this->addViolation(
                $node,
                [
                    $node->getName(),
                ]
            );
        }
    }
}
