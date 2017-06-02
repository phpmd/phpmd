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
 * This rule will check the NPath-complexity of a method or function against the
 * configured threshold.
 */
class NpathComplexity extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * This method checks the acyclic complexity for the given node against a
     * configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('minimum');
        $npath = $node->getMetric('npath');
        if ($npath < $threshold) {
            return;
        }

        $this->addViolation(
            $node,
            array(
                $node->getType(),
                $node->getName(),
                $npath,
                $threshold
            )
        );
    }
}
