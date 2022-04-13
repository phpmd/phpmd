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
class Annotation
{
    /**
     * Name of the suppress warnings annotation.
     */
    const SUPPRESS_ANNOTATION = 'suppressWarnings';

    /**
     * The annotation name.
     *
     * @var string
     */
    private $name = null;

    /**
     * The annotation value.
     *
     * @var string
     */
    private $value = null;

    /**
     * Constructs a new annotation instance.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = trim($value, '" ');
    }

    /**
     * Checks if this annotation suppresses the given rule.
     *
     * @param \PHPMD\Rule $rule
     * @return boolean
     */
    public function suppresses(Rule $rule)
    {
        if (lcfirst($this->name) === self::SUPPRESS_ANNOTATION) {
            return $this->isSuppressed($rule);
        }

        return false;
    }

    /**
     * Checks if this annotation suppresses the given rule.
     *
     * @param \PHPMD\Rule $rule
     * @return boolean
     */
    private function isSuppressed(Rule $rule)
    {
        if (in_array($this->value, array('PHPMD', 'PMD'))) {
            return true;
        } elseif (preg_match(
            '/^(PH)?PMD\.' . preg_replace('/^.*\/([^\/]*)$/', '$1', $rule->getName()) . '/',
            $this->value
        )) {
            return true;
        }

        return (stripos($rule->getName(), $this->value) !== false);
    }
}
