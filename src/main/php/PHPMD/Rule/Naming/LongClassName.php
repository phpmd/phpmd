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
use PHPMD\Rule\InterfaceAware;
use PHPMD\Support\Strings;

/**
 * This rule class will check if a class name doesn't exceed the configured length excluding
 * certain configured suffixes.
 */
class LongClassName extends AbstractRule implements ClassAware, InterfaceAware
{
    /**
     * Temporary cache of configured suffixes to subtract
     *
     * @var string[]|null
     */
    private $subtractSuffixes;

    /**
     * This method checks if a class name exceeds the configured maximum length
     * and emits a rule violation.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('maximum');
        $length    = Strings::length($node->getName(), $this->getSubtractSuffixList());
        if ($length > $threshold) {
            $this->addViolation($node, array($node->getName(), $threshold));
        }
    }

    /**
     * Gets array of suffixes from property
     *
     * @return string[]
     */
    private function getSubtractSuffixList()
    {
        if ($this->subtractSuffixes === null) {
            $this->subtractSuffixes = Strings::split(',', $this->getStringProperty('subtract-suffixes', ''));
        }

        return $this->subtractSuffixes;
    }
}
