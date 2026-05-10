<?php

/**
 * This file is part of PHP Mess Detector.
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD\Rule\Controversial;

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
 * This rule class detects namespace parts that are not named in CamelCase.
 */
final class CamelCaseNamespace extends AbstractRule implements ClassAware, EnumAware, InterfaceAware, TraitAware
{
    /** @var array<string, int> */
    private array $exceptions;

    public function apply(AbstractNode $node): void
    {
        $pattern = '/^[A-Z][a-zA-Z0-9]*$/';
        if ($this->getBooleanProperty('camelcase-abbreviations', false)) {
            // disallow any consecutive uppercase letters
            $pattern = '/^([A-Z][a-z0-9]+)*$/';
        }

        $exceptions = $this->getExceptionsList();
        $fullNamespace = $node->getNamespaceName();
        if (!$fullNamespace) {
            return;
        }
        $namespaceNames = explode('\\', $fullNamespace);

        foreach ($namespaceNames as $namespaceName) {
            if (isset($exceptions[$namespaceName])) {
                continue;
            }

            if (!preg_match($pattern, $namespaceName)) {
                $this->addViolation($node, [$namespaceName, $fullNamespace]);
            }
        }
    }

    /**
     * Gets array of exceptions from property
     * @return array<string, int>
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    private function getExceptionsList(): array
    {
        $this->exceptions ??= array_flip(
            Strings::splitToList($this->getStringProperty('exceptions', ''), ',')
        );

        return $this->exceptions;
    }
}
