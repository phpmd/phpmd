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
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\EnumAware;
use PHPMD\Rule\InterfaceAware;
use PHPMD\Rule\TraitAware;
use PHPMD\Utility\Strings;

/**
 * This rule will detect classes and interfaces with names that are too short.
 */
class ShortClassName extends AbstractRule implements ClassAware, InterfaceAware, TraitAware, EnumAware
{
    /**
     * Temporary cache of configured exceptions. Have name as key
     *
     * @var array<string, int>|null
     */
    protected $exceptions;

    /**
     * Check if a class or interface name is below the minimum configured length and emit a rule violation
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('minimum');
        $classOrInterfaceName = $node->getName();
        if (strlen($classOrInterfaceName) >= $threshold) {
            return;
        }

        $exceptions = $this->getExceptionsList();
        if (isset($exceptions[$classOrInterfaceName])) {
            return;
        }

        $this->addViolation($node, array($classOrInterfaceName, $threshold));
    }

    /**
     * Gets array of exceptions from property
     *
     * @return array<string, int>
     */
    protected function getExceptionsList()
    {
        if ($this->exceptions === null) {
            $this->exceptions = array_flip(
                Strings::splitToList($this->getStringProperty('exceptions', ''), ',')
            );
        }

        return $this->exceptions;
    }
}
