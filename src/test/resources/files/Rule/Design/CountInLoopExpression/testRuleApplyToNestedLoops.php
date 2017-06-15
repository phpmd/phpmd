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

function testRuleApplyToNestedLoops()
{
    $foo = 'foo';

    for ($i = 0; sizeof($i) < 5 || $i < 5; $i++) {
        while ($i !== 'baz' && count($foo) || $i < 5) {
            $foo .= $i;
            do {
                $i += 2;
            } while (5 - 0 < sizeof($foo) && 3 < $foo . 'bar');
            while (count($foo) < 5 && sizeof($foo) < 5 && count($foo) < 5) {
                while (count($_GET) > -1 && 0) {
                    for ($j = 0; $j < count($_GET); $j++) {
                        $i += $j * 2;
                    }
                }
            }
        }
    }

    return $foo;
}
