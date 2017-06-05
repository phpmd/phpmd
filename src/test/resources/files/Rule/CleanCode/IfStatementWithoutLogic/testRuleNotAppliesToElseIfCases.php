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

namespace PHPMDTest;

class testRuleNotAppliesToElseIfCases
{
    public function testRuleNotAppliesToElseIfCases()
    {
        // trigger violation
        if ('foo') {
            // ...
        } elseif ('bar') {
            // ...
        }
        if ('') {
            // ...
        } elseif (null) {
            // ...
        }
        if (true) {
            // ...
        } elseif (false) {
            // ...
        } elseif (3.00 + 1) {
            // ...
        } elseif (5 . '0') {
            // ...
        }

        // don't trigger
        if (rand()) {
            // ...
        } elseif (round(1.00, 1)) {
            // ...
        }
        if (phpversion()) {
            // ...
        } elseif (version_compare(phpversion(), '5.3.0', '>')) {
            // ...
        }
        if (filter_has_var(INPUT_GET, 'foo')) {
            // ...
        } elseif ('foo' . 'bar' . __DIR__) {
            // ...
        }
        if (3 + 3 + 3 + 3 + 3 < time() || 10 < 0) {
            // ...
        } elseif (__FUNCTION__ === 'baz') {
            // ...
        }
    }
}
