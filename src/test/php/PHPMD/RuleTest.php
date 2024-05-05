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
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, '1');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueOn
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueOn(): void
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, 'on');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueTrue
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueTrue(): void
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, 'true');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForDifferentStringValue
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForDifferentStringValue(): void
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, 'True');

        $this->assertFalse($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * Tests the getBooleanProperty method with a fallback value
     *
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsFallbackString(): void
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__, true));
    }

    /**
     * testGetIntPropertyReturnsValueOfTypeInteger
     *
     * @covers ::getIntProperty
     * @covers ::getProperty
     */
    public function testGetIntPropertyReturnsValueOfTypeInteger(): void
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, '42.3');

        $this->assertSame(42, $rule->getIntProperty(__FUNCTION__));
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

        /** @var AbstractRule $rule */
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
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);

        $this->assertSame(123, $rule->getIntProperty(__FUNCTION__, '123'));
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

        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->getBooleanProperty(__FUNCTION__);
    }

    /**
     * testGetStringPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @covers ::getStringProperty
     * @covers ::getProperty
     */
    public function testGetStringPropertyThrowsExceptionWhenNoPropertyForNameExists(): void
    {
        self::expectException(OutOfBoundsException::class);

        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->getStringProperty(__FUNCTION__);
    }

    /**
     * testGetStringPropertyReturnsStringValue
     *
     * @covers ::getStringProperty
     * @covers ::getProperty
     */
    public function testGetStringPropertyReturnsString(): void
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);
        $rule->addProperty(__FUNCTION__, 'Forty Two');

        $this->assertSame('Forty Two', $rule->getStringProperty(__FUNCTION__));
    }

    /**
     * Tests the getStringProperty method with a fallback value
     *
     * @covers ::getStringProperty
     * @covers ::getProperty
     */
    public function testGetStringPropertyReturnsFallbackString(): void
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass(AbstractRule::class);

        $this->assertSame('fallback', $rule->getStringProperty(__FUNCTION__, 'fallback'));
    }
}
