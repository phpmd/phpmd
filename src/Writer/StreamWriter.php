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

namespace PHPMD\Writer;

use PHPMD\AbstractWriter;
use RuntimeException;

/**
 * This writer uses PHP's stream api as its output target.
 */
final class StreamWriter extends AbstractWriter
{
    /**
     * The stream resource handle
     *
     * @var resource
     */
    private $stream;

    /**
     * Constructs a new stream writer instance.
     *
     * @param resource|string $streamResourceOrUri
     * @throws RuntimeException If the output directory cannot be found.
     */
    public function __construct($streamResourceOrUri)
    {
        if (!is_string($streamResourceOrUri)) {
            $this->stream = $streamResourceOrUri;

            return;
        }
        $dirName = dirname($streamResourceOrUri);
        if (!file_exists($dirName)) {
            mkdir($dirName, 0o777, true);
        }
        if (!file_exists($dirName)) {
            $message = 'Cannot find output directory "' . $dirName . '".';

            throw new RuntimeException($message);
        }

        $stream = fopen($streamResourceOrUri, 'wb');
        if (!$stream) {
            throw new RuntimeException('Cannot open "' . $streamResourceOrUri . '".');
        }

        $this->stream = $stream;
    }

    /**
     * The dtor closes the open output resource.
     */
    public function __destruct()
    {
        if ($this->stream !== STDOUT && $this->stream !== STDERR && is_resource($this->stream)) {
            @fclose($this->stream);
        }
    }

    /**
     * Writes the given <b>$data</b> fragment to the wrapper output stream.
     */
    public function write(string $data): void
    {
        fwrite($this->stream, $data);
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }
}
