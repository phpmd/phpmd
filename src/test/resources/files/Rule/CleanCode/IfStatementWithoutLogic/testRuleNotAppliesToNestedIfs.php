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

class testRuleNotAppliesToNestedIfs
{
    const FOO = 'bar';

    private $baz = 1;

    public function testRuleNotAppliesToNestedIfs()
    {
        $arr = array(1, 2, 3, 4, 5);

        // trigger violation
        if ('foo') {
            if (/** foo */ 8 > 0) {
                if ('8') {
                    // ...
                } else {
                    // ...
                }
                if (false) {
                    // ...
                } elseif (true) {
                    // ...
                }
                if (null) {
                    // ...
                }
            }
        } elseif ('bar') {
            if (1) {
                if (0) {
                    if (3 - 1 || 1 ^ 'foo') {
                        // ...
                    }
                } elseif ('baz') {
                    // ...
                }
            }
        }

        // don't trigger
        if (800000 < time()) {
            if (true === round(80.333, 0)) {
                if (__FUNCTION__ . 'foo' == 'bar') {
                    if ($this->baz == $this->doNothing()) {
                        // ...
                    } elseif (self::FOO || static::FOO) {
                        if (self::FOO === 1.00) {
                            if (is_numeric($this->baz)) {
                                // ...
                            }
                        }
                    }
                }
            }
        } elseif (round(1.00, 1)) {
            if ('a' . 'b' . 'c' . 'd' . 'e' >> self::FOO) {
                if (true === $this->baz) {
                    if (!empty($arr)) {
                        if ($arr[0] == $arr[1]) {
                            $name = 'arr';
                            if (is_array($$name)) {
                                // ...
                            }
                        }
                    }
                } elseif (isset($this->bar)) {
                    // ...
                }
            }
        }
    }

    /**
     * For test purposes
     *
     * @return null
     */
    private function doNothing()
    {
        return null;
    }
}
