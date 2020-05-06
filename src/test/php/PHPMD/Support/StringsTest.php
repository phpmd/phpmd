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

use PHPMD\AbstractTest;

/**
 * Test cases for the Strings utility class.
 *
 * @coversDefaultClass  \PHPMD\Support\Strings
 */
class StringsTest extends AbstractTest
{
    /**
     * @return void
     */
    public function testLengthWithoutSuffixesEmptyString()
    {
        static::assertSame(0, Strings::lengthWithoutSuffixes('', array()));
    }

    /**
     * @return void
     */
    public function testLengthWithoutSuffixesEmptyStringWithConfiguredSubtractSuffix()
    {
        static::assertSame(0, Strings::lengthWithoutSuffixes('', array('Foo', 'Bar')));
    }

    /**
     * @return void
     */
    public function testLengthWithoutSuffixesStringWithoutSubtractSuffixMatch()
    {
        static::assertSame(8, Strings::lengthWithoutSuffixes('UnitTest', array('Foo', 'Bar')));
    }

    /**
     * @return void
     */
    public function testLengthWithoutSuffixesStringWithSubtractSuffixMatch()
    {
        static::assertSame(4, Strings::lengthWithoutSuffixes('UnitBar', array('Foo', 'Bar')));
    }

    /**
     * @return void
     */
    public function testLengthWithoutSuffixesStringWithDoubleSuffixMatchSubtractOnce()
    {
        static::assertSame(7, Strings::lengthWithoutSuffixes('UnitFooBar', array('Foo', 'Bar')));
    }

    /**
     * @return void
     */
    public function testLengthWithoutSuffixesStringWithPrefixMatchShouldNotSubtract()
    {
        static::assertSame(11, Strings::lengthWithoutSuffixes('FooUnitTest', array('Foo', 'Bar')));
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testSplitToListEmptySeparatorThrowsException()
    {
        Strings::splitToList('UnitTest', '');
    }

    /**
     * @return void
     */
    public function testSplitToListEmptyString()
    {
        static::assertSame(array(), Strings::splitToList('', ','));
    }

    /**
     * @return void
     */
    public function testSplitToListStringWithoutMatchingSeparator()
    {
        static::assertSame(array('UnitTest'), Strings::splitToList('UnitTest', ','));
    }

    /**
     * @return void
     */
    public function testSplitToListStringWithMatchingSeparator()
    {
        static::assertSame(array('Unit', 'Test'), Strings::splitToList('Unit,Test', ','));
    }

    /**
     * @return void
     */
    public function testSplitToListStringTrimsLeadingAndTrailingWhitespace()
    {
        static::assertSame(array('Unit', 'Test'), Strings::splitToList('Unit , Test', ','));
    }

    /**
     * @return void
     */
    public function testSplitToListStringRemoveEmptyStringValues()
    {
        static::assertSame(array('Foo'), Strings::splitToList('Foo,,,', ','));
    }

    /**
     * @return void
     */
    public function testSplitToListStringShouldNotRemoveAZeroValue()
    {
        static::assertSame(array('0', '1', '2'), Strings::splitToList('0,1,2', ','));
    }
}
