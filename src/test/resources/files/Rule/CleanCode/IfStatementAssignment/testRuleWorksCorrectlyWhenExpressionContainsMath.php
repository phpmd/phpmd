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

class Foo
{

    public function testRuleWorksCorrectlyWhenExpressionContainsMath()
    {
        $foo = 0;
        if ($foo == 1 * 1) { // not applied
            // ...
        }
        if ($foo == 1 % 2) { // not applied
            // ...
        }
        if ($foo == 1 + 2 + 2 / 1) { // not applied
            // ...
        }
        if ($foo == 'foo' . 'bar') { // not applied
            // ...
        }
        if ($foo == ('' . '')) { // not applied
            // ...
        }
        if ($foo = 1 * 1) { // applied
            // ...
        }
        if ($foo = '' . 1) { // applied
            // ...
        }
        if ($foo = (int)1.0 + (float)false % (string)1) { // applied
            // ...
        }
    }
}
