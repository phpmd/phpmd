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
 * This rule class detects violations of Coupling Between Objects metric.
 *
 * @since 1.1.0
 */
final class CouplingBetweenObjects extends AbstractRule implements ClassAware
{
    /**
     * This method should implement the violation analysis algorithm of concrete
     * rule implementations. All extending classes must implement this method.
     */
    public function apply(AbstractNode $node): void
    {
        $cbo = $node->getMetric('cbo');
        $threshold = $this->getIntProperty('maximum');
        if ($cbo >= $threshold) {
            $this->addViolation($node, [$node->getName(), (string) $cbo, (string) $threshold]);
        }
    }
}
