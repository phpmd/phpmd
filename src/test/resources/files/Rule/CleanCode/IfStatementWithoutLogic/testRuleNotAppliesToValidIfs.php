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

function testRuleNotAppliesToValidIfs()
{
    $foo = 'bar';

    $baz = 1;

    $xyz = 'abc';

    $arr = array(1, 2, 3, 4, 5);

    if (1 == rand()) {
        // ...
    }
    if ('' === time()) {
        // ...
    }
    if ('100' ^ $xyz) {
        // ...
    }
    if (0 || $xyz) {
        // ...
    }
    if ('0' << $baz) {
        // ...
    }
    if ('1' == $xyz) {
        // ...
    }
    if (~true | $xyz or (50 % $baz) >> 1 << 1 | 'foo' ^ /** ignore */ $foo) {
        // ...
    }
    if (false || true && false || $xyz + $xyz) {
        // ...
    }
    if (null !== $xyz) {
        // ...
    }
    if (null === null || $xyz === null) {
        // ...
    }
    if (isset($arr, $foo)) {
        // ...
    }
    if (count($arr)) {
        // ...
    }
    if ($arr[0]) {
        // ...
    }
}
