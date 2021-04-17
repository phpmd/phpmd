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

namespace PHPMD\Rule\Naming;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule class will detect methods and functions with very short names.
 */
class ShortMethodName extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * Extracts all variable and variable declarator nodes from the given node
     * and checks the variable name length against the configured minimum
     * length.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('minimum');
        if ($threshold <= strlen($node->getName())) {
            return;
        }

        $exceptions = $this->getExceptionsList();

        if (in_array($node->getName(), $exceptions)) {
            return;
        }

        $this->addViolation(
            $node,
            array(
                $node->getParentName(),
                $node->getName(),
                $threshold,
            )
        );
    }

    /**
     * Gets array of exceptions from property
     *
     * @return array
     */
    protected function getExceptionsList()
    {
        try {
            $exceptions = $this->getStringProperty('exceptions');
        } catch (\OutOfBoundsException $e) {
            $exceptions = '';
        }

        return explode(',', $exceptions);
    }
}
