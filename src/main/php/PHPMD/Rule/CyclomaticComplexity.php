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
 * This rule checks a given method or function against the configured cyclomatic
 * complexity threshold.
 */
class CyclomaticComplexity extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * This method checks the cyclomatic complexity for the given node against
     * a configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('reportLevel');
        $ccn = $node->getMetric('ccn2');
        if ($ccn < $threshold) {
            return;
        }

        $this->addViolation(
            $node,
            array(
                $node->getType(),
                $node->getName(),
                $ccn,
                $threshold
            )
        );
    }
}
