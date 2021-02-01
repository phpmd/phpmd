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

class testRuleAppliesToMethodMatchingFunctionName
{
    public function testRuleAppliesToMethodMatchingFunctionName()
    {
        $this->preg_match('a', 'b', $undefined);
    }

    public function preg_match($a, $b, $c)
    {
        // noop
    }
}
