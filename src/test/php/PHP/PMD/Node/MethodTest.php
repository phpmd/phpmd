<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009-2011, Manuel Pichler <mapi@phpmd.org>.
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
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Node
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/PMD/Node/Method.php';

/**
 * Test case for the method node implementation.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Node
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Node_MethodTest extends PHP_PMD_AbstractTest
{
    /**
     * testMagicCallDelegatesToWrappedPHPDependMethod
     *
     * @return void
     * @covers PHP_PMD_Node_AbstractCallable::__call
     * @group phpmd
     * @group phpmd::node
     * @group unittest
     */
    public function testMagicCallDelegatesToWrappedPHPDependMethod()
    {
        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null));
        $method->expects($this->once())
            ->method('getStartLine');

        $node = new PHP_PMD_Node_Method($method);
        $node->getStartLine();
    }

    /**
     * testMagicCallThrowsExceptionWhenNoMatchingMethodExists
     *
     * @return void
     * @covers PHP_PMD_Node_AbstractCallable::__call
     * @group phpmd
     * @group phpmd::node
     * @group unittest
     * @expectedException BadMethodCallException
     */
    public function testMagicCallThrowsExceptionWhenNoMatchingMethodExists()
    {
        $node = new PHP_PMD_Node_Method(new PHP_Depend_Code_Method(null));
        $node->getFooBar();
    }

    /**
     * testGetParentTypeReturnsInterfaceForInterfaceMethod
     *
     * @return void
     * @covers PHP_PMD_Node_Method::getParentType
     * @group phpmd
     * @group phpmd::node
     * @group unittest
     */
    public function testGetParentTypeReturnsInterfaceForInterfaceMethod()
    {
        $method = $this->getMethod();
        self::assertInstanceOf(PHP_PMD_Node_Interface::CLAZZ, $method->getParentType());
    }

    /**
     * testGetParentTypeReturnsClassForClassMethod
     *
     * @return void
     * @covers PHP_PMD_Node_Method::getParentType
     * @group phpmd
     * @group phpmd::node
     * @group unittest
     */
    public function testGetParentTypeReturnsClassForClassMethod()
    {
        $method = $this->getMethod();
        self::assertInstanceOf(PHP_PMD_Node_Class::CLAZZ, $method->getParentType());
    }

    /**
     * testHasSuppressWarningsExecutesDefaultImplementation
     *
     * @return void
     * @covers PHP_PMD_Node_Method::hasSuppressWarningsAnnotationFor
     * @group phpmd
     * @group phpmd::node
     * @group unittest
     */
    public function testHasSuppressWarningsExecutesDefaultImplementation()
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        $this->assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsDelegatesToParentClassMethod
     *
     * @return void
     * @covers PHP_PMD_Node_Method::hasSuppressWarningsAnnotationFor
     * @group phpmd
     * @group phpmd::node
     * @group unittest
     */
    public function testHasSuppressWarningsDelegatesToParentClassMethod()
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        $this->assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsDelegatesToParentInterfaceMethod
     *
     * @return void
     * @covers PHP_PMD_Node_Method::hasSuppressWarningsAnnotationFor
     * @group phpmd
     * @group phpmd::node
     * @group unittest
     */
    public function testHasSuppressWarningsDelegatesToParentInterfaceMethod()
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        $this->assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }
}
