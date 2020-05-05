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
 * Test case for the Strings utility class.
 *
 * @covers \PHPMD\Support\Strings
 */
class StringsTest extends AbstractTest
{
    /**
     * @return void
     */
    public function testLengthEmptyString()
    {
        static::assertSame(0, Strings::length(''));
    }

    /**
     * @return void
     */
    public function testLengthEmptyStringWithConfiguredSubtractSuffix()
    {
        static::assertSame(0, Strings::length('', array('Foo', 'Bar')));
    }

    /**
     * @return void
     */
    public function testLengthStringWithoutSubtractSuffixMatch()
    {
        static::assertSame(8, Strings::length('UnitTest', array('Foo', 'Bar')));
    }

    /**
     * @return void
     */
    public function testLengthStringWithSubtractSuffixMatch()
    {
        static::assertSame(4, Strings::length('UnitBar', array('Foo', 'Bar')));
    }

    /**
     * @return void
     */
    public function testLengthStringWithDoubleSuffixMatchSubtractOnce()
    {
        static::assertSame(7, Strings::length('UnitFooBar', array('Foo', 'Bar')));
    }

    /**
     * @return void
     */
    public function testLengthStringWithPrefixMatchShouldNotSubtract()
    {
        static::assertSame(11, Strings::length('FooUnitTest', array('Foo', 'Bar')));
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testSplitEmptySeparatorThrowsException()
    {
        Strings::split('', 'UnitTest');
    }

    /**
     * @return void
     */
    public function testSplitEmptyString()
    {
        static::assertSame(array(), Strings::split(',', ''));
    }

    /**
     * @return void
     */
    public function testSplitStringWithoutMatchingSeparator()
    {
        static::assertSame(array('UnitTest'), Strings::split(',', 'UnitTest'));
    }

    /**
     * @return void
     */
    public function testSplitStringWithMatchingSeparator()
    {
        static::assertSame(array('Unit', 'Test'), Strings::split(',', 'Unit,Test'));
    }

    /**
     * @return void
     */
    public function testSplitStringTrimsLeadingAndTrailingWhitespace()
    {
        static::assertSame(array('Unit', 'Test'), Strings::split(',', 'Unit , Test'));
    }

    /**
     * @return void
     */
    public function testSplitStringRemoveEmptyStringValues()
    {
        static::assertSame(array('Foo'), Strings::split(',', 'Foo,,,'));
    }

    /**
     * @return void
     */
    public function testSplitStringShouldNotRemoveAZeroValue()
    {
        static::assertSame(array('0', '1', '2'), Strings::split(',', '0,1,2'));
    }
}
