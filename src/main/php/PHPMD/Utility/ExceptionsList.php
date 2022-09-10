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

namespace PHPMD\Utility;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use PHPMD\Rule;
use RuntimeException;

class ExceptionsList implements IteratorAggregate, ArrayAccess
{
    /**
     * Temporary cache of configured exceptions. Have name as key
     *
     * @var array<string, int>|null
     */
    protected $exceptions;

    /**
     * Rule to which the exception list apply.
     *
     * @var Rule
     */
    protected $rule;

    /**
     * Extra characters to be trimmed with whitespace at beginning and ending of each exception.
     *
     * @var string
     */
    protected $trim;

    /**
     * Separator used to join exception in the property string.
     *
     * @var string
     */
    protected $separator;

    /**
     * @param Rule $rule
     * @param string $trim
     * @param string $separator
     */
    public function __construct(Rule $rule, $trim = '', $separator = ',')
    {
        $this->rule = $rule;
        $this->trim = $trim;
        $this->separator = $separator;
    }

    /**
     * @param string $value
     * @return boolean
     */
    public function contains($value)
    {
        $exceptions = $this->getExceptionsList();

        return isset($exceptions[Strings::trim($value, $this->trim)]);
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
                Strings::splitToList(
                    $this->rule->getStringProperty('exceptions', ''),
                    $this->separator,
                    $this->trim
                )
            );
        }

        return $this->exceptions;
    }

    public function getIterator()
    {
        $keys = array_keys($this->getExceptionsList());

        return new ArrayIterator(array_combine($keys, $keys));
    }

    public function offsetExists($offset)
    {
        return $this->contains($offset);
    }

    public function offsetGet($offset)
    {
        $exceptions = $this->getExceptionsList();

        return $exceptions[Strings::trim($offset, $this->trim)];
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException(__CLASS__ . ' is read-only');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException(__CLASS__ . ' is read-only');
    }
}
