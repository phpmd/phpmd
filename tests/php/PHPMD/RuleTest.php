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

namespace PHPMD;

use OutOfBoundsException;

/**
 * Test case for the {@link \PHPMD\AbstractRule} class.
 *
 * @coversDefaultClass \PHPMD\AbstractRule
 */
class RuleTest extends AbstractTestCase
{
    /**
     * testGetBooleanPropertyReturnsTrueForStringValue1
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValue1(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, '1');

        static::assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueOn
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueOn(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, 'on');

        static::assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueTrue
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueTrue(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, 'true');

        static::assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForDifferentStringValue
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForDifferentStringValue(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, 'True');

        static::assertFalse($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * Tests the getBooleanProperty method with a fallback value
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsFallbackString(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);

        static::assertTrue($rule->getBooleanProperty(__FUNCTION__, true));
    }

    /**
     * testGetIntPropertyReturnsValueOfTypeInteger
     *
     * @covers ::getIntProperty
     * @covers ::getProperty
     */
    public function testGetIntPropertyReturnsValueOfTypeInteger(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, '42.3');

        static::assertSame(42, $rule->getIntProperty(__FUNCTION__));
    }

    /**
     * testGetIntPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @covers ::getIntProperty
     * @covers ::getProperty
     */
    public function testGetIntPropertyThrowsExceptionWhenNoPropertyForNameExists(): void
    {
        self::expectException(OutOfBoundsException::class);

        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->getIntProperty(__FUNCTION__);
    }

    /**
     * Tests the getIntProperty method with a fallback value
     *
     * @covers ::getIntProperty
     * @covers ::getProperty
     */
    public function testGetIntPropertyReturnsFallbackString(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);

        static::assertSame(123, $rule->getIntProperty(__FUNCTION__, 123));
    }

    /**
     * testGetBooleanPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyThrowsExceptionWhenNoPropertyForNameExists(): void
    {
        self::expectException(OutOfBoundsException::class);

        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->getBooleanProperty(__FUNCTION__);
    }

    /**
     * testGetStringPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @covers ::getProperty
     * @covers ::getStringProperty
     */
    public function testGetStringPropertyThrowsExceptionWhenNoPropertyForNameExists(): void
    {
        self::expectException(OutOfBoundsException::class);

        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->getStringProperty(__FUNCTION__);
    }

    /**
     * testGetStringPropertyReturnsStringValue
     *
     * @covers ::getProperty
     * @covers ::getStringProperty
     */
    public function testGetStringPropertyReturnsString(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, 'Forty Two');

        static::assertSame('Forty Two', $rule->getStringProperty(__FUNCTION__));
    }

    /**
     * Tests the getStringProperty method with a fallback value
     *
     * @covers ::getProperty
     * @covers ::getStringProperty
     */
    public function testGetStringPropertyReturnsFallbackString(): void
    {
        $rule = $this->getMockForAbstractClass(AbstractRule::class);

        static::assertSame('fallback', $rule->getStringProperty(__FUNCTION__, 'fallback'));
    }
}
