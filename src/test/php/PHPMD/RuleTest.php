<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD;

/**
 * Test case for the {@link \PHPMD\AbstractRule} class.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
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
