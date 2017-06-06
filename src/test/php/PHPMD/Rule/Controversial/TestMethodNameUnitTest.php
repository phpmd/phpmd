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
 *_Design
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule\Controversial;

/**
 * @author    Jeroen De Dauw <jeroendedauw@gmail.com>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Rule\Controversial\TestMethodName
 * @group phpmd
 * @group phpmd::rule
 * @group phpmd::rule::controversial
 * @group unittest
 */
class TestMethodNameUnitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider validMethodProvider
     */
    public function testValidMethodsMatch($methodName)
    {
        $rule = new TestMethodName();
        $this->assertTrue($rule->isValidTestMethod($methodName));
    }

    public function validMethodProvider()
    {
        return array(
            'No underscore, one word' =>
                array('testSomething'),

            'No underscore, multiple words' =>
                array('testGivenFoobarKittensAppear'),

            'No underscore, digits are allowed' =>
                array('testG1v3nF00b4rK1tte3sApp34r'),

            'Minimal word count' =>
                array('testGivenFoobar_kittensAppear'),

            'Non-minimal word count' =>
                array('testGivenFoobarFoobarFoobar_kittensStillMostDefinitelyAppear'),

            'Digits are allowed' =>
                array('testG1v3nF00b4r_k1tte3sApp34r'),
        );
    }

    /**
     * @dataProvider invalidMethodProvider
     */
    public function testInvalidMethodsDoNotMatch($methodName)
    {
        $rule = new TestMethodName();
        $this->assertFalse($rule->isValidTestMethod($methodName));
    }

    public function invalidMethodProvider()
    {
        return array(
            'Does not start with test' =>
                array('givenFoobarKittensAppear'),

            'Only has test' =>
                array('test'),

            'First first letter after test is lowercase' =>
                array('testgivenFoobar'),

            'First first letter after test is digit)' =>
                array('test5ivenFoobar'),

            'Too few words before underscore' =>
                array('testGiven_kittensAppear'),

            'Too few words after underscore' =>
                array('testGivenFoobar_kittens'),

            'First first letter after test is lowercase (using underscore)' =>
                array('testgivenFoobar_kittensAppear'),

            'First first letter after test is digit (using underscore)' =>
                array('test5ivenFoobar_kittensAppear'),

            'First letter after underscore is uppercase' =>
                array('testGivenFoobar_KittensAppear'),

            'Nothing after underscore' =>
                array('testGivenFoobar_'),

            'Double underscore' =>
                array('testGivenFoobar__kittensAppear'),

            'Two underscores' =>
                array('testGivenFoobar_kittensAppear_allOverTheInternet'),

        );
    }
}
