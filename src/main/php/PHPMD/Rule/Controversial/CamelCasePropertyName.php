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
use PHPMD\Rule\TraitAware;
use PHPMD\Node\ClassNode;
use PHPMD\Node\TraitNode;

/**
 * This rule class detects properties not named in camelCase.
 *
 * @author Francis Besset <francis.besset@gmail.com>
 * @since 1.1.0
 */
class CamelCasePropertyName extends AbstractRule implements ClassAware, TraitAware
{
    /**
     * This method checks if a property is not named in camelCase
     * and emits a rule violation.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof ClassNode && !$node instanceof TraitNode) {
            return;
        }

        foreach ($node->getProperties() as $property) {
            $propertyName = $property->getName();

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

    private function isValid($propertyName)
    {
        // disallow any consecutive uppercase letters
        if ($this->getBooleanProperty('camelcase-abbreviations', false)
            && preg_match('/[A-Z]{2}/', $propertyName) === 1) {
            return false;
        }

        if ($this->getBooleanProperty('allow-underscore')) {
            return preg_match('/^\$[_]?[a-z][a-zA-Z0-9]*$/', $propertyName);
        }

        return preg_match('/^\$[a-z][a-zA-Z0-9]*$/', $propertyName);
    }
}
