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

namespace PHPMD\Rule;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;

/**
 * This rule checks the number of public methods and fields in a given class.
 * Then it compares the number of public members against a configured threshold.
 */
class ExcessivePublicCount extends AbstractRule implements ClassAware, TraitAware
{
    /**
     * This method checks the number of public fields and methods in the given
     * class and checks that value against a configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('minimum');
        $cis = $node->getMetric('cis');
        if ($cis < $threshold) {
            return;
        }
        $this->addViolation(
            $node,
            array(
                $node->getType(),
                $node->getName(),
                $cis,
                $threshold,
            )
        );
    }
}
