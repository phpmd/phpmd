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

namespace PHPMD\Stubs;

use PHPMD\AbstractWriter;

/**
 * Simple test implementation of PHPMD's writer.
 */
class WriterStub extends AbstractWriter
{
    /**
     * The written data chunks.
     *
     * @var array
     */
    public $chunks = array();

    /**
     * Writes a data string to the concrete output.
     *
     * @param string $data The data to write.
     *
     * @return void
     */
    public function write($data)
    {
        $this->chunks[] = $data;
    }

    /**
     * Returns a concated string of all data chunks.
     *
     * @return string
     */
    public function getData()
    {
        return implode('', $this->chunks);
    }

    /**
     * Returns the written data chunks.
     *
     * @return array
     */
    public function getChunks()
    {
        return $this->chunks;
    }
}
