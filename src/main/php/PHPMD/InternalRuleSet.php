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

namespace PHPMD;

final class InternalRuleSet
{
    /** @var list<string>|null */
    private static ?array $names = null;

    /**
     * @return list<string>
     */
    public static function getNames(): array
    {
        return self::$names ??= array_map(
            static fn(string $path) => pathinfo($path, PATHINFO_FILENAME),
            glob(__DIR__ . '/../../resources/rulesets/*.xml'),
        );
    }

    public static function getNamesConcatenated(): string
    {
        return implode(',', self::getNames());
    }
}
