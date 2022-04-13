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

/**
 * Test case for the {@link \PHPMD\AbstractRule} class.
 *
 * @coversDefaultClass \PHPMD\AbstractRule
 */
class RuleTest extends AbstractTest
{
    /**
     * testGetBooleanPropertyReturnsTrueForStringValue1
     *
     * @return void
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValue1()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, '1');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueOn
     *
     * @return void
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueOn()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, 'on');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueTrue
     *
     * @return void
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueTrue()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, 'true');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForDifferentStringValue
     *
     * @return void
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsTrueForDifferentStringValue()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, 'True');

        $this->assertFalse($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * Tests the getBooleanProperty method with a fallback value
     *
     * @return void
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyReturnsFallbackString()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__, true));
    }

    /**
     * testGetIntPropertyReturnsValueOfTypeInteger
     *
     * @return void
     * @covers ::getIntProperty
     * @covers ::getProperty
     */
    public function testGetIntPropertyReturnsValueOfTypeInteger()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, '42.3');

        $this->assertSame(42, $rule->getIntProperty(__FUNCTION__));
    }

    /**
     * testGetIntPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @return void
     * @expectedException \OutOfBoundsException
     * @covers ::getIntProperty
     * @covers ::getProperty
     */
    public function testGetIntPropertyThrowsExceptionWhenNoPropertyForNameExists()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->getIntProperty(__FUNCTION__);
    }

    /**
     * Tests the getIntProperty method with a fallback value
     *
     * @return void
     * @covers ::getIntProperty
     * @covers ::getProperty
     */
    public function testGetIntPropertyReturnsFallbackString()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');

        $this->assertSame(123, $rule->getIntProperty(__FUNCTION__, '123'));
    }

    /**
     * testGetBooleanPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @return void
     * @expectedException \OutOfBoundsException
     * @covers ::getBooleanProperty
     * @covers ::getProperty
     */
    public function testGetBooleanPropertyThrowsExceptionWhenNoPropertyForNameExists()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->getBooleanProperty(__FUNCTION__);
    }

    /**
     * testGetStringPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @return void
     * @expectedException \OutOfBoundsException
     * @covers ::getStringProperty
     * @covers ::getProperty
     */
    public function testGetStringPropertyThrowsExceptionWhenNoPropertyForNameExists()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->getStringProperty(__FUNCTION__);
    }

    /**
     * testGetStringPropertyReturnsStringValue
     *
     * @return void
     * @covers ::getStringProperty
     * @covers ::getProperty
     */
    public function testGetStringPropertyReturnsString()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, 'Fourty Two');

        $this->assertSame('Fourty Two', $rule->getStringProperty(__FUNCTION__));
    }

    /**
     * Tests the getStringProperty method with a fallback value
     *
     * @return void
     * @covers ::getStringProperty
     * @covers ::getProperty
     */
    public function testGetStringPropertyReturnsFallbackString()
    {
        /** @var AbstractRule $rule */
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');

        $this->assertSame('fallback', $rule->getStringProperty(__FUNCTION__, 'fallback'));
    }
}
