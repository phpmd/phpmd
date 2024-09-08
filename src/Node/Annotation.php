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

namespace PHPMD\Node;

use PHPMD\Rule;

/**
 * Simple code annotation class.
 */
final class Annotation
{
    /** Name of the suppress warnings annotation. */
    private const SUPPRESS_ANNOTATION = 'suppressWarnings';

    /** The annotation value. */
    private readonly string $value;

    /**
     * Constructs a new annotation instance.
     *
     * @param string $name The annotation name.
     */
    public function __construct(
        private readonly string $name,
        string $value,
    ) {
        $this->value = trim($value, '" ');
    }

    /**
     * Checks if this annotation suppresses the given rule.
     */
    public function suppresses(Rule $rule): bool
    {
        if (lcfirst($this->name) === self::SUPPRESS_ANNOTATION) {
            return $this->isSuppressed($rule);
        }

        return false;
    }

    /**
     * Checks if this annotation suppresses the given rule.
     */
    private function isSuppressed(Rule $rule): bool
    {
        if (in_array($this->value, ['PHPMD', 'PMD'], true)) {
            return true;
        }
        if (
            preg_match(
                '/^(PH)?PMD\.' . preg_replace('/^.*\/([^\/]*)$/', '$1', $rule->getName()) . '/',
                $this->value
            )
        ) {
            return true;
        }

        return (stripos($rule->getName(), $this->value) !== false);
    }
}
