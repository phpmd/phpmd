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

function testRuleAppliesToIfsWithLiteralsOnly()
{
    if (1) {
        // ...
    }
    if ('') {
        // ...
    }
    if ('100') {
        // ...
    }
    if (0) {
        // ...
    }
    if ('0') {
        // ...
    }
    if ('1') {
        // ...
    }
    if (true) {
        // ...
    }
    if (false) {
        // ...
    }
    if (null) {
        // ...
    }
    if (null === null) {
        // ...
    }
    if (2 > 0) {
        // ...
    }
    if (4 >> 1) {
        // ...
    }
    if ('5' /** foo */) {
        // ...
    }
    if (6 << 0) {
        // ...
    }
}
