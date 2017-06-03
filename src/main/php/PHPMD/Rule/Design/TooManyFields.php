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
 * This rule class will detect all classes with too much fields.
 */
class TooManyFields extends AbstractRule implements ClassAware
{
    /**
     * This method checks the number of methods with in a given class and checks
     * this number against a configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('maxfields');
        $vars = $node->getMetric('vars');
        if ($vars <= $threshold) {
            return;
        }
        $this->addViolation(
            $node,
            array(
                $node->getType(),
                $node->getName(),
                $vars,
                $threshold
            )
        );
    }
}
