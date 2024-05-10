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
use InvalidArgumentException;
use IteratorAggregate;
use OutOfBoundsException;
use PHPMD\Rule;
use RuntimeException;

/**
 * @implements IteratorAggregate<string, string>
 * @implements ArrayAccess<string, string>
 */
class ExceptionsList implements IteratorAggregate, ArrayAccess
{
    /**
     * Separator used to join exception in the property string.
     */
    protected string $separator;
    /**
     * Temporary cache of configured exceptions. Have name as key
     *
     * @var array<string, string>
     */
    private array $exceptions;

    /**
     * Rule to which the exception list apply.
     */
    private Rule $rule;

    /**
     * Extra characters to be trimmed with whitespace at beginning and ending of each exception.
     */
    private string $trim;

    public function __construct(Rule $rule, string $trim = '', string $separator = ',')
    {
        $this->rule = $rule;
        $this->trim = $trim;
        $this->separator = $separator;
    }

    /**
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function contains(string $value): bool
    {
        $exceptions = $this->getExceptionsList();

        return isset($exceptions[Strings::trim($value, $this->trim)]);
    }

    /**
     * Gets array of exceptions from property
     *
     * @return array<string, string>
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    protected function getExceptionsList(): array
    {
        if (!isset($this->exceptions)) {
            $values = Strings::splitToList(
                $this->rule->getStringProperty('exceptions', ''),
                $this->separator,
                $this->trim
            );

            $this->exceptions = array_combine($values, $values);
        }

        return $this->exceptions;
    }

    /**
     * @return ArrayIterator<string, string>
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getExceptionsList());
    }

    /**
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function offsetExists($offset): bool
    {
        return $this->contains($offset);
    }

    /**
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset): string
    {
        $exceptions = $this->getExceptionsList();
        $value = $exceptions[Strings::trim($offset, $this->trim)] ?? null;

        if ($value === null) {
            throw new OutOfBoundsException('Exception "' . $offset . '" offset does not exist.');
        }

        return $value;
    }

    /**
     * @throws RuntimeException
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException(__CLASS__ . ' is read-only');
    }

    /**
     * @throws RuntimeException
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException(__CLASS__ . ' is read-only');
    }
}
