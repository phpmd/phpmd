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
 *
 * @since 1.2.1
 */
class ProcessingError
{
    /** The source file where the processing error occurred. */
    private readonly string $file;

    /**
     * Constructs a new processing error instance.
     *
     * @param string $message The original processing error message.
     */
    public function __construct(
        private readonly string $message,
    ) {
        $this->file = $this->extractFile($message);
    }

    /**
     * Returns the source file where the processing error occurred.
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Returns the original processing error message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Evil hack that extracts the source file from the original exception
     * message. This method should be removed once we have added the source file
     * as a mandatory property to PDepend's exceptions.
     */
    private function extractFile(string $message): string
    {
        preg_match('(file: (.+)\.$| file "([^"]+)")', $message, $match);

        $match = array_values(array_filter($match));

        return $match[1] ?? '';
    }
}
