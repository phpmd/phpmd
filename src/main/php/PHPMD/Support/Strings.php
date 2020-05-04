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

namespace PHPMD\Support;

use InvalidArgumentException;

/**
 * Utility class to provide string checks and manipulations
 */
class Strings
{
    /**
     * Returns the length of the variable name, excluding at most one suffix.
     *
     * @param string $variableName Variable name to calculate the length for.
     * @param array $subtractSuffixes Optional list of suffixes to exclude from the calculated length.
     * @return int The length of the string, without suffix, if applicable.
     */
    public static function length($variableName, array $subtractSuffixes = array())
    {
        $variableNameLength = strlen($variableName);

        foreach ($subtractSuffixes as $suffix) {
            $suffixLength = strlen($suffix);
            if (substr($variableName, -$suffixLength) === $suffix) {
                $variableNameLength -= $suffixLength;
                break;
            }
        }

        return $variableNameLength;
    }

    /**
     * Split a string with the given separator, trim whitespaces around the parts and remove any empty strings
     *
     * @param string $separator The separator to split the string with, similar to explode
     * @param string $string The string to split
     * @return array The trimmed and filtered parts of the string
     * @throws InvalidArgumentException When the separator is an empty string
     */
    public static function split($separator, $string)
    {
        if ($separator === '') {
            throw new InvalidArgumentException('Separator can\'t me empty string');
        }

        return array_filter(
            array_map('trim', explode($separator, $string)),
            function ($value) {
                return $value !== '';
            }
        );
    }
}
