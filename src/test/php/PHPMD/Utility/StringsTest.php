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
use PHPMD\AbstractTestCase;
use Throwable;

/**
 * Test cases for the Strings utility class.
 *
 * @coversDefaultClass  \PHPMD\Utility\Strings
 */
class StringsTest extends AbstractTestCase
{
    /**
     * Tests the lengthWithoutSuffixes() method with an empty string
     *
     * @throws Throwable
     */
    public function testLengthWithoutSuffixesEmptyString(): void
    {
        static::assertSame(0, Strings::lengthWithoutSuffixes('', []));
    }

    /**
     * Tests the lengthWithoutSuffixes() method with an empty string with list of suffixes
     *
     * @throws Throwable
     */
    public function testLengthWithoutSuffixesEmptyStringWithConfiguredSubtractSuffix(): void
    {
        static::assertSame(0, Strings::lengthWithoutSuffixes('', ['Foo', 'Bar']));
    }

    /**
     * Tests the lengthWithoutSuffixes() method with a string not in the list of suffixes
     *
     * @throws Throwable
     */
    public function testLengthWithoutSuffixesStringWithoutSubtractSuffixMatch(): void
    {
        static::assertSame(8, Strings::lengthWithoutSuffixes('UnitTest', ['Foo', 'Bar']));
    }

    /**
     * Tests the lengthWithoutSuffixes() method with a string in the list of suffixes
     *
     * @throws Throwable
     */
    public function testLengthWithoutSuffixesStringWithSubtractSuffixMatch(): void
    {
        static::assertSame(4, Strings::lengthWithoutSuffixes('UnitBar', ['Foo', 'Bar']));
    }

    /**
     * Tests the lengthWithoutSuffixes() method with a string that should match only once for two potential matches
     *
     * @throws Throwable
     */
    public function testLengthWithoutSuffixesStringWithDoubleSuffixMatchSubtractOnce(): void
    {
        static::assertSame(7, Strings::lengthWithoutSuffixes('UnitFooBar', ['Foo', 'Bar']));
    }

    /**
     * Tests the lengthWithoutSuffixes() method that a Prefix should not be matched
     *
     * @throws Throwable
     */
    public function testLengthWithoutSuffixesStringWithPrefixMatchShouldNotSubtract(): void
    {
        static::assertSame(11, Strings::lengthWithoutSuffixes('FooUnitTest', ['Foo', 'Bar']));
    }

    /**
     * Tests the lengthWithoutSuffixes() method that a Prefix should be matched
     *
     * @throws Throwable
     */
    public function testlengthWithPrefixesAndSuffixesStringWithPrefixMatchShouldSubtract(): void
    {
        static::assertSame(11, Strings::lengthWithoutSuffixes('FooUnitTest', ['Foo', 'Bar']));
        static::assertSame(8, Strings::lengthWithoutSuffixes('UnitTestFoo', ['Foo', 'Bar']));
    }

    /**
     * Tests the lengthWithoutPrefixesAndSuffixes() method that a Prefix should not be matched in order
     *
     * @throws Throwable
     */
    public function testlengthWithPrefixesAndSuffixesStringWithPrefixesMatchShouldSubtractInOrder(): void
    {
        $prefixes = ['Foo', 'Bar'];
        $suffixes = ['Foo', 'FooUnit'];
        $length = Strings::lengthWithoutPrefixesAndSuffixes('FooUnitTest', $suffixes, $prefixes);
        static::assertSame(8, $length);
    }

    /**
     * Tests the splitToList() method with an empty separator
     *
     * @throws Throwable
     */
    public function testSplitToListEmptySeparatorThrowsException(): void
    {
        self::expectExceptionObject(new InvalidArgumentException(
            "Separator can't be empty string",
        ));

        Strings::splitToList('UnitTest', '');
    }

    /**
     * Tests the splitToList() method with an empty string
     *
     * @throws Throwable
     */
    public function testSplitToListEmptyString(): void
    {
        static::assertSame([], Strings::splitToList('', ','));
    }

    /**
     * Tests the splitToList() method with a non-matching separator
     *
     * @throws Throwable
     */
    public function testSplitToListStringWithoutMatchingSeparator(): void
    {
        static::assertSame(['UnitTest'], Strings::splitToList('UnitTest', ','));
    }

    /**
     * Tests the splitToList() method with a matching separator
     *
     * @throws Throwable
     */
    public function testSplitToListStringWithMatchingSeparator(): void
    {
        static::assertSame(['Unit', 'Test'], Strings::splitToList('Unit,Test', ','));
    }

    /**
     * Tests the splitToList() method with trailing whitespace
     *
     * @throws Throwable
     */
    public function testSplitToListStringTrimsLeadingAndTrailingWhitespace(): void
    {
        static::assertSame(['Unit', 'Test'], Strings::splitToList('Unit , Test', ','));
    }

    /**
     * Tests the splitToList() method that it removes empty strings from list
     *
     * @throws Throwable
     */
    public function testSplitToListStringRemoveEmptyStringValues(): void
    {
        static::assertSame(['Foo'], Strings::splitToList('Foo,,,', ','));
    }

    /**
     * Tests the splitToList() method that it does not remove zero values from list
     *
     * @throws Throwable
     */
    public function testSplitToListStringShouldNotRemoveAZeroValue(): void
    {
        static::assertSame(['0', '1', '2'], Strings::splitToList('0,1,2', ','));
    }
}
