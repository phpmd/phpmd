<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
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
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://phpmd.org
 * @since     1.2.1
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/PMD/ProcessingError.php';

/**
 * Test case for the processing error class.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://phpmd.org
 * @since     1.2.1
 *
 * @covers PHP_PMD_ProcessingError
 * @group phpmd
 * @group unittest
 */
class PHP_PMD_ProcessingErrorTest extends PHP_PMD_AbstractTest
{
    /**
     * testGetMessageReturnsTheExpectedValue
     *
     * @return void
     */
    public function testGetMessageReturnsTheExpectedValue()
    {
        $processingError = new PHP_PMD_ProcessingError('Hello World.');
        $this->assertEquals('Hello World.', $processingError->getMessage());
    }

    /**
     * Tests that the processing error class extracts the source filename from
     * a given exception message,
     *
     * @param string $message The original exception message
     *
     * @return void
     * @dataProvider getParserExceptionMessages
     */
    public function testGetFileReturnsExpectedFileName($message)
    {
        $processingError = new PHP_PMD_ProcessingError($message);
        $this->assertEquals('/tmp/foo.php', $processingError->getFile());
    }

    /**
     * Data provider that returns common exception messages used by PHP_Depend's
     * parser.
     *
     * @return array
     */
    public function getParserExceptionMessages()
    {
        return array(
            array(
                'The parser has reached an invalid state near line "42" in file ' .
                '"/tmp/foo.php". Please check the following conditions: message'
            ),
            array(
                'Unexpected token: >, line: 42, col: 23, file: /tmp/foo.php.'
            ),
            array(
                'Unexpected end of token stream in file: /tmp/foo.php.'
            ),
            array(
                'Missing default value on line: 42, col: 23, file: /tmp/foo.php.'
            )
        );
    }
}
