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

use InvalidArgumentException;

/**
 * Utility class to provide string checks and manipulations
 */
final class Strings
{
    /**
     * Returns the length of the given string, excluding at most one suffix
     *
     * @param string $stringName String to calculate the length for.
     * @param array<int, string> $subtractSuffixes List of suffixes to exclude from the calculated length.
     * @return int The length of the string, without suffix, if applicable.
     */
    public static function lengthWithoutSuffixes(string $stringName, array $subtractSuffixes): int
    {
        return self::lengthWithoutPrefixesAndSuffixes($stringName, [], $subtractSuffixes);
    }

    /**
     * Returns the length of the given string, excluding at most one suffix
     *
     * @param string $stringName String to calculate the length for.
     * @param array<int, string> $subtractPrefixes List of prefixes to exclude from the calculated length.
     * @param array<int, string> $subtractSuffixes List of suffixes to exclude from the calculated length.
     * @return int The length of the string, without suffix, if applicable.
     */
    public static function lengthWithoutPrefixesAndSuffixes(
        string $stringName,
        array $subtractPrefixes,
        array $subtractSuffixes
    ): int {
        $stringLength = strlen($stringName);

        foreach ($subtractSuffixes as $suffix) {
            $suffixLength = strlen($suffix);
            if (substr($stringName, -$suffixLength) === $suffix) {
                $stringLength -= $suffixLength;

                break;
            }
        }

        foreach ($subtractPrefixes as $prefix) {
            $prefixLength = strlen($prefix);
            if (strncmp($stringName, $prefix, $prefixLength) === 0) {
                $stringLength -= $prefixLength;

                break;
            }
        }

        return $stringLength;
    }

    /**
     * Split a string with the given separator, trim whitespaces around the parts and remove any empty strings
     *
     * @param string $listAsString The string to split.
     * @param string $separator The separator to split the string with, similar to explode.
     * @param string $trim Extra character to be trimmed off of each value.
     * @return array<int, string> The list of trimmed and filtered parts of the string.
     * @throws InvalidArgumentException When the separator is an empty string.
     */
    public static function splitToList(string $listAsString, string $separator = ',', string $trim = ''): array
    {
        if ($separator === '') {
            throw new InvalidArgumentException("Separator can't be empty string");
        }

        return array_filter(
            array_map(
                static fn($value) => Strings::trim($value, $trim),
                explode($separator, $listAsString)
            ),
            static fn($value) => $value !== ''
        );
    }

    /**
     * Trim " \t\n\r\0\x0B" and specified extra characters from start and end of a string.
     */
    public static function trim(string $value, string $trim = ''): string
    {
        return trim($value, " \t\n\r\0\x0B$trim");
    }
}
