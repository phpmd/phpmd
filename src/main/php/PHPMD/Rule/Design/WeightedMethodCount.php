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
 * This rule checks a given class against a configured weighted method count
 * threshold.
 *
 * @since      0.2.5
 */
class WeightedMethodCount extends AbstractRule implements ClassAware
{
    /**
     * This method checks the weighted method count for the given class against
     * a configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('maximum');
        $actual    = $node->getMetric('wmc');

        if ($actual >= $threshold) {
            $this->addViolation($node, array($node->getName(), $actual, $threshold));
        }
    }
}
