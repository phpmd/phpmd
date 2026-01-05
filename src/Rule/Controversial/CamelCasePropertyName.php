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
use PHPMD\Node\ClassNode;
use PHPMD\Node\TraitNode;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\TraitAware;

/**
 * This rule class detects properties not named in camelCase.
 *
 * @author Francis Besset <francis.besset@gmail.com>
 * @since 1.1.0
 */
final class CamelCasePropertyName extends AbstractRule implements ClassAware, TraitAware
{
    /**
     * This method checks if a property is not named in camelCase
     * and emits a rule violation.
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof ClassNode && !$node instanceof TraitNode) {
            return;
        }

        foreach ($node->getProperties() as $property) {
            $propertyName = $property->getImage();

            if (!$this->isValid($propertyName)) {
                $this->addViolation(
                    $node,
                    [
                        $propertyName,
                    ]
                );
            }
        }
    }

    /**
     * @throws OutOfBoundsException
     */
    private function isValid(string $propertyName): bool
    {
        // disallow any consecutive uppercase letters
        if (
            $this->getBooleanProperty('camelcase-abbreviations', false)
            && preg_match('/[A-Z]{2}/', $propertyName) === 1
        ) {
            return false;
        }

        if ($this->getBooleanProperty('allow-underscore')) {
            return preg_match('/^\$[_]?[a-z][a-zA-Z0-9]*$/', $propertyName) === 1;
        }

        return preg_match('/^\$[a-z][a-zA-Z0-9]*$/', $propertyName) === 1;
    }
}
