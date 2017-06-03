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
 * This rule will detect class that have to much direct child classes.
 */
class NumberOfChildren extends AbstractRule implements ClassAware
{
    /**
     * This method checks the number of classes derived from the given class
     * node.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $nocc = $node->getMetric('nocc');
        $threshold = $this->getIntProperty('minimum');
        if ($nocc >= $threshold) {
            $this->addViolation(
                $node,
                array(
                    $node->getType(),
                    $node->getName(),
                    $nocc,
                    $threshold
                )
            );
        }
    }
}
