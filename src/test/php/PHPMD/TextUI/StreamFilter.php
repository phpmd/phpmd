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

namespace PHPMD\TextUI;

/**
 * Utility to test stream related stuff
 */
class StreamFilter extends \php_user_filter
{
    public static $streamHandle;

    public function filter($in, $out, &$consumed, $closing)
    {
        self::$streamHandle = '';

        while ($bucket = stream_bucket_make_writeable($in)) {
            self::$streamHandle .= $bucket->data;
            $consumed += $bucket->datalen;
        }

        return PSFS_PASS_ON;
    }
}
