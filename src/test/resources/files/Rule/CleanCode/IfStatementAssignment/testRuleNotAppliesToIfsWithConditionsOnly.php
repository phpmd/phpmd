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

namespace PHPMDTest;

class Foo
{

    public function testRuleNotAppliesToIfsWithConditionsOnly()
    {
        $foo = 'bar';

        if ($foo == 'bar') { // not applied
            // ...
        }
        if ($foo === 'bar') { // not applied
            // ...
        }
        if ($foo != 'bar') { // not applied
            // ...
        }
        if ($foo !== 'bar') { // not applied
            // ...
        }
        if ($foo > 1) { // not applied
            // ...
        }
        if ($foo >= 1) { // not applied
            // ...
        }
        if ($foo < 1) { // not applied
            // ...
        }
        if ($foo <= 1) { // not applied
            // ...
        }
    }
}
