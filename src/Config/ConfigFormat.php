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

enum ConfigFormat: string
{
    case Yaml = 'yml';
    case Json = 'json';
    case Php = 'php';
    case Xml = 'xml';

    public static function fromFileName(string $fileName): self
    {
        if (!preg_match('/\.(?<format>php|json|ya?ml)(?:\.dist)?$/i', $fileName, $match)) {
            return self::Xml;
        }

        return match (strtolower($match['format'])) {
            'php' => self::Php,
            'json' => self::Json,
            default => self::Yaml,
        };
    }
}
