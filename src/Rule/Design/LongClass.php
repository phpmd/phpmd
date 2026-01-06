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

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

/**
 * This rule class will detect excessive long classes.
 */
final class LongClass extends AbstractRule implements ClassAware
{
    /**
     * This method checks the length of the given class node against a configured
     * threshold.
     */
    public function apply(AbstractNode $node): void
    {
        $threshold = $this->getIntProperty('minimum');

        $loc = -1;
        if ($this->getBooleanProperty('ignore-whitespace')) {
            $loc = $node->getMetric('eloc');
        }
        if (-1 === $loc) {
            $loc = $node->getMetric('loc');
        }

        if ($loc < $threshold) {
            return;
        }

        $this->addViolation($node, [$node->getName(), (string) $loc, (string) $threshold]);
    }
}
