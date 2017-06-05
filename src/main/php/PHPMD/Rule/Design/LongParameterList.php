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
 * This rule class checks for excessive long function and method parameter lists.
 */
class LongParameterList extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * This method checks the number of arguments for the given function or method
     * node against a configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('minimum');
        $count = $node->getParameterCount();
        if ($count < $threshold) {
            return;
        }

        $this->addViolation(
            $node,
            array(
                $node->getType(),
                $node->getName(),
                $count,
                $threshold
            )
        );
    }
}
