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
 * @covers \PHPMD\AbstractRule
 * @group phpmd
 * @group unittest
 */
class RuleTest extends AbstractTest
{
    /**
     * testGetIntPropertyReturnsValueOfTypeInteger
     *
     * @return void
     */
    public function testGetIntPropertyReturnsValueOfTypeInteger()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, '42.3');

        $this->assertSame(42, $rule->getIntProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValue1
     *
     * @return void
     */
    public function testGetBooleanPropertyReturnsTrueForStringValue1()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, '1');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueOn
     *
     * @return void
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueOn()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, 'on');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForStringValueTrue
     *
     * @return void
     */
    public function testGetBooleanPropertyReturnsTrueForStringValueTrue()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, 'true');

        $this->assertTrue($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetBooleanPropertyReturnsTrueForDifferentStringValue
     *
     * @return void
     */
    public function testGetBooleanPropertyReturnsTrueForDifferentStringValue()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, 'True');

        $this->assertFalse($rule->getBooleanProperty(__FUNCTION__));
    }

    /**
     * testGetIntPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @return void
     * @expectedException \OutOfBoundsException
     */
    public function testGetIntPropertyThrowsExceptionWhenNoPropertyForNameExists()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->getIntProperty(__FUNCTION__);
    }

    /**
     * testGetBooleanPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @return void
     * @expectedException \OutOfBoundsException
     */
    public function testGetBooleanPropertyThrowsExceptionWhenNoPropertyForNameExists()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->getBooleanProperty(__FUNCTION__);
    }

    /**
     * testStringPropertyThrowsExceptionWhenNoPropertyForNameExists
     *
     * @return void
     * @expectedException \OutOfBoundsException
     */
    public function testGetStringPropertyThrowsExceptionWhenNoPropertyForNameExists()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->getStringProperty(__FUNCTION__);
    }

    /**
     * testGetStringPropertyReturnsStringValue
     *
     * @return void
     */
    public function testGetStringPropertyReturnsString()
    {
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        $rule->addProperty(__FUNCTION__, 'Fourty Two');

        $this->assertSame('Fourty Two', $rule->getStringProperty(__FUNCTION__));
    }
}
