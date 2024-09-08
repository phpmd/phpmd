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
use PHPMD\Rule\ClassAware;

/**
 * This rule will detect classes that are too deep in the inheritance tree.
 */
final class DepthOfInheritance extends AbstractRule implements ClassAware
{
    /**
     * This method checks the number of parents for the given class
     * node.
     */
    public function apply(AbstractNode $node): void
    {
        try {
            $threshold = $this->getIntProperty('maximum');
            $comparison = 1;
        } catch (OutOfBoundsException) {
            $threshold = $this->getIntProperty('minimum');
            $comparison = 2;
        }

        $dit = $node->getMetric('dit');
        if (
            ($comparison === 1 && $dit > $threshold) ||
            ($comparison === 2 && $dit >= $threshold)
        ) {
            $this->addViolation(
                $node,
                [
                    $node->getType(),
                    $node->getName(),
                    (string) $dit,
                    (string) $threshold,
                ]
            );
        }
    }
}
