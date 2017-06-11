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

function testRuleAppliesCorrectlyWithNestedArrays()
{
    return array(
        'foo' => 40,
        'foo' => 42,
        'foo' => array(
            'foo' => 43,
            array(
                'foo' => 44,
                array(
                    'foo' => 45,
                    'foo' => 46,
                ),
            ),
        ),
        array(
            'foo' => 47,
            'foo' => 49,
        )
    );
}
