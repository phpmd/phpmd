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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD\Config;

final class ConfigFileFinder
{
    /** @var list<string> */
    private const FILES = [
        'phpmd.yml',
        'phpmd.yaml',
        'phpmd.json',
        'phpmd.xml',
        'phpmd.php',
    ];

    public function find(string $directory = ''): ?string
    {
        $prefix = $directory === '' ? '' : rtrim($directory, '/\\') . DIRECTORY_SEPARATOR;

        foreach (self::FILES as $file) {
            // Search for phpmd.yml, .phpmd.yml and phpmd.yml.dist
            foreach ([$file, ".$file", "$file.dist"] as $path) {
                if (file_exists($prefix . $path)) {
                    return $prefix . $path;
                }
            }
        }

        return null;
    }
}
