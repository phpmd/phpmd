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

/**
 * This writer uses PHP's stream api as its output target.
 */
class StreamWriter extends AbstractWriter
{
    /**
     * The stream resource handle
     *
     * @var resource
     */
    private $stream = null;

    /**
     * Constructs a new stream writer instance.
     *
     * @param resource|string $streamResourceOrUri
     */
    public function __construct($streamResourceOrUri)
    {
        if (is_resource($streamResourceOrUri) === true) {
            $this->stream = $streamResourceOrUri;
        } else {
            $dirName = dirname($streamResourceOrUri);
            if (file_exists($dirName) === false) {
                mkdir($dirName, 0777, true);
            }
            if (file_exists($dirName) === false) {
                $message = 'Cannot find output directory "' . $dirName . '".';
                throw new \RuntimeException($message);
            }

            $this->stream = fopen($streamResourceOrUri, 'wb');
        }
    }

    /**
     * The dtor closes the open output resource.
     */
    public function __destruct()
    {
        if ($this->stream !== STDOUT && is_resource($this->stream) === true) {
            @fclose($this->stream);
        }
        $this->stream = null;
    }

    /**
     * Writes the given <b>$data</b> fragment to the wrapper output stream.
     *
     * @param string $data
     * @return void
     */
    public function write($data)
    {
        fwrite($this->stream, $data);
    }
}
