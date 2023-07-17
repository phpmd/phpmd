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
 * This rule checks if an interface or class name exceeds the configured length excluding certain configured suffixes
 */
class LongClassName extends AbstractRule implements ClassAware, InterfaceAware, TraitAware, EnumAware
{
    /**
     * Temporary cache of configured suffixes to subtract
     *
     * @var string[]|null
     */
    protected $subtractSuffixes;

    /**
     * Check if a class name exceeds the configured maximum length and emit a rule violation
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('maximum');
        $classOrInterfaceName = $node->getName();
        if (Strings::lengthWithoutSuffixes($classOrInterfaceName, $this->getSubtractSuffixList()) <= $threshold) {
            return;
        }
        $this->addViolation($node, array($classOrInterfaceName, $threshold));
    }

    /**
     * Gets array of suffixes from property
     *
     * @return string[]
     */
    protected function getSubtractSuffixList()
    {
        if ($this->subtractSuffixes === null) {
            $this->subtractSuffixes = Strings::splitToList(
                $this->getStringProperty('subtract-suffixes', ''),
                ','
            );
        }

        return $this->subtractSuffixes;
    }
}
