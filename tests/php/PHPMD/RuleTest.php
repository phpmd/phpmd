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
     * @covers ::getProperty
     * @covers ::isTruthyProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValue1(): void
    {
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
        $rule->addProperty(__FUNCTION__, '1');

        static::assertTrue($rule->isTruthyProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueOn
     *
     * @covers ::getProperty
     * @covers ::isTruthyProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueOn(): void
    {
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
        $rule->addProperty(__FUNCTION__, 'on');

        static::assertTrue($rule->isTruthyProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueTrue
     *
     * @covers ::getProperty
     * @covers ::isTruthyProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueTrue(): void
    {
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
        $rule->addProperty(__FUNCTION__, 'true');

        static::assertTrue($rule->isTruthyProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForDifferentStringValue
     *
     * @covers ::getProperty
     * @covers ::isTruthyProperty
     */
    public function testGetBooleanPropertyReturnsTrueForDifferentStringValue(): void
    {
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
        $rule->addProperty(__FUNCTION__, 'True');

        static::assertFalse($rule->isTruthyProperty(__FUNCTION__));
    }

    /**
     * Tests the isTruthyProperty method with a fallback value
     *
     * @covers ::getProperty
     * @covers ::isTruthyProperty
     */
    public function testGetBooleanPropertyReturnsFallbackString(): void
    {
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();

        static::assertTrue($rule->isTruthyProperty(__FUNCTION__, true));
    }

    /**
     * testGetIntPropertyReturnsValueOfTypeInteger
     *
     * @covers ::getIntProperty
     * @covers ::getProperty
     */
    public function testGetIntPropertyReturnsValueOfTypeInteger(): void
    {
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
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

        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
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
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();

        static::assertSame(123, $rule->getIntProperty(__FUNCTION__, 123));
    }

    /**
     * testGetBooleanPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @covers ::getProperty
     * @covers ::isTruthyProperty
     */
    public function testGetBooleanPropertyThrowsExceptionWhenNoPropertyForNameExists(): void
    {
        self::expectException(OutOfBoundsException::class);

        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
        $rule->isTruthyProperty(__FUNCTION__);
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

        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
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
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();
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
        $rule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['apply'])
            ->getMock();

        static::assertSame('fallback', $rule->getStringProperty(__FUNCTION__, 'fallback'));
    }
}
