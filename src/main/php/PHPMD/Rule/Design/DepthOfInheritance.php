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
 * This rule will detect classes that are to deep in the inheritance tree.
 */
class DepthOfInheritance extends AbstractRule implements ClassAware
{
    /**
     * This method checks the number of parents for the given class
     * node.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        try {
            $threshold = $this->getIntProperty('maximum');
            $comparision = 1;
        } catch (\OutOfBoundsException $e) {
            $threshold = $this->getIntProperty('minimum');
            $comparision = 2;
        }

        $dit = $node->getMetric('dit');
        if (($comparision === 1 && $dit > $threshold) ||
            ($comparision === 2 && $dit >= $threshold)
        ) {
            $this->addViolation(
                $node,
                array(
                    $node->getType(),
                    $node->getName(),
                    $dit,
                    $threshold,
                )
            );
        }
    }
}
