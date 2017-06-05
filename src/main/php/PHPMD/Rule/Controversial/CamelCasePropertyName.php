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

/**
 * This rule class detects properties not named in camelCase.
 *
 * @author     Francis Besset <francis.besset@gmail.com>
 * @since      1.1.0
 */
class CamelCasePropertyName extends AbstractRule implements ClassAware
{
    /**
     * This method checks if a property is not named in camelCase
     * and emits a rule violation.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $allowUnderscore = $this->getBooleanProperty('allow-underscore');

        $pattern = '/^\$[a-zA-Z][a-zA-Z0-9]*$/';
        if ($allowUnderscore == true) {
            $pattern = '/^\$[_]?[a-zA-Z][a-zA-Z0-9]*$/';
        }

        foreach ($node->getProperties() as $property) {
            $propertyName = $property->getName();

            if (!preg_match($pattern, $propertyName)) {
                $this->addViolation(
                    $node,
                    array(
                        $propertyName,
                    )
                );
            }
        }
    }
}
