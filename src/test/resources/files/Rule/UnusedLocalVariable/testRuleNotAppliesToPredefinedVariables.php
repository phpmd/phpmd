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

class testRuleNotAppliesToPredefinedVariables
{
    public function testRuleNotAppliesToPredefinedVariables()
    {
        $foo = 'bar';
        $headers = array();
        foreach ($http_response_header as $header) {
            $headers[] = $header;
            if (null !== $php_errormsg) {
                continue;
            }
        }

        return $headers;
    }
}
