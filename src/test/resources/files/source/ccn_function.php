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
 * @license https://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

/**
 * Test function with a high cyclomatic complexity
 */
function ccn_function($arg)
{
    switch ($arg) {
        case 1:
            for ($i = 0; $i < 10; ++$i) {
                if ($i % 2 === 0) {
                    if ($arg - $i < 0) {
                        echo "foo";
                    }
                }
            }
            break;
        case 2:
            while (true) {
                if (time() % 5 === 0 && time() % 2 === 0) {
                    break;
                } else {
                    if (time() % 7 === 0) {
                        $x = true;
                        for ($i = 0; $i < 42; ++$i) {
                            $x = $x || true;
                        }

                        return $x;
                    }
                }

                return 23;
            }
            break;
    }
}
