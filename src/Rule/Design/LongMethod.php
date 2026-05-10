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
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule will detect to long methods, those methods are unreadable and in
 * many cases the result of copy and paste coding.
 */
final class LongMethod extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * This method checks the lines of code length for the given function or
     * method node against a configured threshold.
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

        $this->addViolation(
            $node,
            [
                $node->getType(),
                $node->getName(),
                (string) $loc,
                (string) $threshold,
            ]
        );
    }
}
