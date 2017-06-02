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

function testRuleAppliesToMultilevelIfConditions()
{
    if (1 || 0) { // not applied
        if (1 == 1 || $foo = 'baz') { // applied
            // ...
        } elseif (1 != array() && 'foo' != 'baz' && $bar = 'baz') { // applied
            // ...
        } elseif (1 % 2 !== !false && $baz = 1 + 1 + 1 - 3) { // applied
            // ...
            if ($foo == 'baz') { // not applied
                // ...
                if (3 - 2 == 3) { // not applied
                    // ...
                    if (true) { // not applied
                        // ...
                        if (1) { // not applied
                            // ...
                        } elseif ($foo = 1) { // applied
                            // ...
                        } elseif ($foo = 2) { // applied
                            // ...
                        } elseif (5 % 5 == 0) { // not applied
                            // ...
                        }
                    }
                }
            }
        }
    }
    if (1 == 1 || 1 && 0 and 4 % 2 || ($foo = 1) xor 5 * 4 * 3 * 2 * 1) { // applied
        // ...
    }
}
