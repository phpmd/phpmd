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
 * Simple data class that we use to keep parsing errors for the report renderer.
 * @since     1.2.1
 */
class ProcessingError
{
    /**
     * The original processing error message.
     *
     * @var string
     */
    private $message;

    /**
     * The source file where the processing error occurred.
     *
     * @var string
     */
    private $file;

    /**
     * Constructs a new processing error instance.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
        $this->file    = $this->extractFile($message);
    }

    /**
     * Returns the source file where the processing error occurred.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Returns the original processing error message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Evil hack that extracts the source file from the original exception
     * message. This method should be removed once we have added the source file
     * as a mandatory property to PDepend's exceptions.
     *
     * @param string $message
     * @return string
     */
    private function extractFile($message)
    {
        preg_match('(file: (.+)\.$| file "([^"]+)")', $message, $match);

        $match = array_values(array_filter($match));
        if (isset($match[1])) {
            return $match[1];
        }
        return '';
    }
}
