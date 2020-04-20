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
 * Test case for the processing error class.
 *
 * @since 1.2.1
 *
 * @covers \PHPMD\ProcessingError
 */
class ProcessingErrorTest extends AbstractTest
{
    /**
     * testGetMessageReturnsTheExpectedValue
     *
     * @return void
     */
    public function testGetMessageReturnsTheExpectedValue()
    {
        $processingError = new ProcessingError('Hello World.');
        $this->assertEquals('Hello World.', $processingError->getMessage());
    }

    /**
     * Tests that the processing error class extracts the source filename from
     * a given exception message,
     *
     * @param string $message The original exception message
     * @return void
     * @dataProvider getParserExceptionMessages
     */
    public function testGetFileReturnsExpectedFileName($message)
    {
        $processingError = new ProcessingError($message);
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
