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

require_once __DIR__ . '/../../../vendor/autoload.php';

if (class_exists('PHPUnit\Framework\TestCase') && ! class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('PHPUnit\Framework\MockObject\MockBuilder', 'PHPUnit_Framework_MockObject_MockBuilder');
    class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
    class_alias('PHPUnit\Runner\Version', 'PHPUnit_Runner_Version');
}

spl_autoload_register(
    function ($class) {
        $file = __DIR__ . '/' . strtr($class, '\\', '/') . '.php';
        if (file_exists($file)) {
            include $file;
        }
    }
);
