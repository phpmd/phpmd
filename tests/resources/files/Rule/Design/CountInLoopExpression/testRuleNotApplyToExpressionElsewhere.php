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

class testRuleNotApplyToExpressionElsewhere
{
    public function testRuleNotApplyToExpressionElsewhere()
    {
        $foo = 'foo';
        $bar = 'bar';

        for ($i = 0; $i < 5; $i++) {
            $foo .= $i;
        }

        if (sizeof($foo) < 5) {
            $bar = $foo;
        }

        for ($j = 0; $j < 1; $j++) {
            if (count($bar)) {
                return $foo;
            } elseif (sizeof($foo)) {
                return $bar;
            }
        }

        return $bar;
    }
}
