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

$replacements = array(
    /**
     * Patch phpunit/phpunit-mock-objects Generator.php file to not create double nullable tokens: `??`
     */
    __DIR__ . '/../../../vendor/phpunit/phpunit-mock-objects/src/Framework/MockObject/Generator.php' => array(
        array(
            "if (version_compare(PHP_VERSION, '7.1', '>=') && " .
                "\$parameter->allowsNull() && !\$parameter->isVariadic()) {",
            "if (version_compare(PHP_VERSION, '7.1', '>=') && version_compare(PHP_VERSION, '8.0', '<') && " .
                "\$parameter->allowsNull() && !\$parameter->isVariadic()) {",
        ),
    ),
    /**
     * Fix phpunit/phpunit to not trigger warning on `final private function`
     */
    __DIR__ . '/../../../vendor/phpunit/phpunit/src/Util/Configuration.php' => array(
        array(
            'final private function',
            'private function',
        ),
    ),
);

foreach ($replacements as $file => $patterns) {
    echo "$file: ";

    if (!file_exists($file)) {
        echo "File not found.\n";

        continue;
    }

    foreach ($patterns as $replacement) {
        list($from, $to) = $replacement;

        $contents = @file_get_contents($file) ?: '';
        $newContents = str_replace($from, $to, $contents);

        if ($newContents !== $contents) {
            file_put_contents($file, $newContents);
            echo "Content changed.\n";

            continue;
        }

        echo "Replace pattern not found.\n";
    }
}

require_once __DIR__ . '/../../../vendor/autoload.php';

spl_autoload_register(
    function ($class) {
        $file = __DIR__ . '/' . strtr($class, '\\', '/') . '.php';
        if (file_exists($file)) {
            include $file;
        }
    }
);
