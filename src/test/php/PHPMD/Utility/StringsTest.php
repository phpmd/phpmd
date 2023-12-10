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

use PHPMD\AbstractTestCase;
use PHPMD\Utility\Strings;

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
     * @return void
     */
    public function testLengthWithoutSuffixesEmptyString()
    {
        static::assertSame(0, Strings::lengthWithoutSuffixes('', array()));
    }

    /**
     * Tests the lengthWithoutSuffixes() method with an empty string with list of suffixes
     *
     * @return void
     */
    public function testLengthWithoutSuffixesEmptyStringWithConfiguredSubtractSuffix()
    {
        static::assertSame(0, Strings::lengthWithoutSuffixes('', array('Foo', 'Bar')));
    }

    /**
     * Tests the lengthWithoutSuffixes() method with a string not in the list of suffixes
     *
     * @return void
     */
    public function testLengthWithoutSuffixesStringWithoutSubtractSuffixMatch()
    {
        static::assertSame(8, Strings::lengthWithoutSuffixes('UnitTest', array('Foo', 'Bar')));
    }

    /**
     * Tests the lengthWithoutSuffixes() method with a string in the list of suffixes
     *
     * @return void
     */
    public function testLengthWithoutSuffixesStringWithSubtractSuffixMatch()
    {
        static::assertSame(4, Strings::lengthWithoutSuffixes('UnitBar', array('Foo', 'Bar')));
    }

    /**
     * Tests the lengthWithoutSuffixes() method with a string that should match only once for two potential matches
     *
     * @return void
     */
    public function testLengthWithoutSuffixesStringWithDoubleSuffixMatchSubtractOnce()
    {
        static::assertSame(7, Strings::lengthWithoutSuffixes('UnitFooBar', array('Foo', 'Bar')));
    }

    /**
     * Tests the lengthWithoutSuffixes() method that a Prefix should not be matched
     *
     * @return void
     */
    public function testLengthWithoutSuffixesStringWithPrefixMatchShouldNotSubtract()
    {
        static::assertSame(11, Strings::lengthWithoutSuffixes('FooUnitTest', array('Foo', 'Bar')));
    }

    /**
     * Tests the lengthWithoutSuffixes() method that a Prefix should be matched
     *
     * @return void
     */
    public function testlengthWithPrefixesAndSuffixesStringWithPrefixMatchShouldSubtract()
    {
        static::assertSame(11, Strings::lengthWithoutSuffixes('FooUnitTest', array('Foo', 'Bar')));
        static::assertSame(8, Strings::lengthWithoutSuffixes('UnitTestFoo', array('Foo', 'Bar')));
    }

    /**
     * Tests the lengthWithoutPrefixesAndSuffixes() method that a Prefix should not be matched in order
     *
     * @return void
     */
    public function testlengthWithPrefixesAndSuffixesStringWithPrefixesMatchShouldSubtractInOrder()
    {
        $prefixes = array('Foo', 'Bar');
        $suffixes = array('Foo', 'FooUnit');
        $length = Strings::lengthWithoutPrefixesAndSuffixes('FooUnitTest', $suffixes, $prefixes);
        static::assertSame(8, $length);
    }

    /**
     * Tests the splitToList() method with an empty separator
     *
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testSplitToListEmptySeparatorThrowsException()
    {
        Strings::splitToList('UnitTest', '');
    }

    /**
     * Tests the splitToList() method with an empty string
     *
     * @return void
     */
    public function testSplitToListEmptyString()
    {
        static::assertSame(array(), Strings::splitToList('', ','));
    }

    /**
     * Tests the splitToList() method with a non-matching separator
     *
     * @return void
     */
    public function testSplitToListStringWithoutMatchingSeparator()
    {
        static::assertSame(array('UnitTest'), Strings::splitToList('UnitTest', ','));
    }

    /**
     * Tests the splitToList() method with a matching separator
     *
     * @return void
     */
    public function testSplitToListStringWithMatchingSeparator()
    {
        static::assertSame(array('Unit', 'Test'), Strings::splitToList('Unit,Test', ','));
    }

    /**
     * Tests the splitToList() method with trailing whitespace
     *
     * @return void
     */
    public function testSplitToListStringTrimsLeadingAndTrailingWhitespace()
    {
        static::assertSame(array('Unit', 'Test'), Strings::splitToList('Unit , Test', ','));
    }

    /**
     * Tests the splitToList() method that it removes empty strings from list
     *
     * @return void
     */
    public function testSplitToListStringRemoveEmptyStringValues()
    {
        static::assertSame(array('Foo'), Strings::splitToList('Foo,,,', ','));
    }

    /**
     * Tests the splitToList() method that it does not remove zero values from list
     *
     * @return void
     */
    public function testSplitToListStringShouldNotRemoveAZeroValue()
    {
        static::assertSame(array('0', '1', '2'), Strings::splitToList('0,1,2', ','));
    }
}
