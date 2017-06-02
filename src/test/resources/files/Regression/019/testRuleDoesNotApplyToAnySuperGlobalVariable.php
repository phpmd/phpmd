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

class testRuleDoesNotApplyToAnySuperGlobalVariable
{
    function testRuleDoesNotApplyToAnySuperGlobalVariable()
    {
        $GLOBALS = 42;
        $HTTP_RAW_POST_DATA = 42;
        $_COOKIE = 42;
        $_ENV = 42;
        $_FILES = 42;
        $_GET = 42;
        $_POST = 42;
        $_REQUEST = 42;
        $_SERVER = 42;
        $_SESSION = 42;
        $argc = 42;
        $argv = 42;
    }
}
