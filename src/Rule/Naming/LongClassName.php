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

use InvalidArgumentException;
use OutOfBoundsException;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\EnumAware;
use PHPMD\Rule\InterfaceAware;
use PHPMD\Rule\TraitAware;
use PHPMD\Utility\Strings;

/**
 * This rule checks if an interface or class name exceeds the configured length
 * excluding certain configured prefixes and suffixes
 */
final class LongClassName extends AbstractRule implements ClassAware, EnumAware, InterfaceAware, TraitAware
{
    /**
     * Temporary cache of configured prefixes to subtract
     *
     * @var array<int, string>
     */
    private array $subtractPrefixes;

    /**
     * Temporary cache of configured suffixes to subtract
     *
     * @var array<int, string>
     */
    private array $subtractSuffixes;

    /**
     * Check if a class name exceeds the configured maximum length and emit a rule violation
     */
    public function apply(AbstractNode $node): void
    {
        $threshold = $this->getIntProperty('maximum');
        $classOrInterfaceName = $node->getName();
        $length = Strings::lengthWithoutPrefixesAndSuffixes(
            $classOrInterfaceName,
            $this->getSubtractPrefixList(),
            $this->getSubtractSuffixList()
        );

        if ($length <= $threshold) {
            return;
        }
        $this->addViolation($node, [$classOrInterfaceName, (string) $threshold]);
    }

    /**
     * Gets array of prefixes from property
     *
     * @return array<int, string>
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function getSubtractPrefixList(): array
    {
        $this->subtractPrefixes ??= Strings::splitToList(
            $this->getStringProperty('subtract-prefixes', ''),
            ','
        );

        return $this->subtractPrefixes;
    }

    /**
     * Gets array of suffixes from property
     *
     * @return array<int, string>
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function getSubtractSuffixList(): array
    {
        $this->subtractSuffixes ??= Strings::splitToList(
            $this->getStringProperty('subtract-suffixes', '')
        );

        return $this->subtractSuffixes;
    }
}
