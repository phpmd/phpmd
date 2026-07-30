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

namespace PHPMD\TextUI;

/**
 * Compatibility shim for GrumPHP, PhpStorm and other tools that still call PHPMD
 * with the pre-3.x positional command line:
 *
 * phpmd <file>,... <format> <ruleset>,... [--exclude <patterns>] [--suffixes <suffixes>] [...]
 *
 * The arguments are rewritten into the current "analyze" sub-command invocation.
 */
final class LegacyArguments
{
    /**
     * Renderer formats accepted in the second positional argument.
     *
     * @var list<string>
     */
    private const FORMATS = [
        'ansi',
        'checkstyle',
        'github',
        'githubcheckruns',
        'gitlab',
        'html',
        'json',
        'sarif',
        'text',
        'xml',
    ];

    /**
     * Options whose comma-separated value is expanded into repeated options.
     *
     * @var list<string>
     */
    private const REPEATABLE_OPTIONS = ['--exclude', '--suffixes'];

    /**
     * Tells whether the given arguments use the legacy positional command line.
     *
     * @param list<string> $argv
     */
    public static function isLegacyInvocation(array $argv): bool
    {
        return isset($argv[1], $argv[2], $argv[3])
            && in_array($argv[2], self::FORMATS, true)
            && $argv[1] !== 'analyze'
            && !str_starts_with($argv[1], '-')
            && !str_starts_with($argv[3], '-');
    }

    /**
     * Rewrites legacy positional arguments into an "analyze" sub-command invocation.
     *
     * The comma-separated file list becomes separate path arguments, each ruleset
     * becomes a repeated "--ruleset" option, and the comma-separated values of any
     * trailing "--exclude" / "--suffixes" option are expanded into repeated options.
     * Every other trailing argument is passed through unchanged.
     *
     * @param list<string> $argv
     * @return list<string>
     */
    public static function transform(array $argv): array
    {
        $newArgv = [$argv[0], 'analyze', ...explode(',', $argv[1]), '--format', $argv[2], '--no-progress'];

        foreach (explode(',', $argv[3]) as $ruleset) {
            $newArgv[] = '--ruleset';
            $newArgv[] = $ruleset;
        }

        for ($i = 4, $argc = count($argv); $i < $argc; $i++) {
            if (in_array($argv[$i], self::REPEATABLE_OPTIONS, true) && isset($argv[$i + 1])) {
                foreach (explode(',', $argv[$i + 1]) as $value) {
                    $newArgv[] = $argv[$i];
                    $newArgv[] = $value;
                }
                $i++;

                continue;
            }
            $newArgv[] = $argv[$i];
        }

        return $newArgv;
    }

    /**
     * Builds the deprecation notice printed for a legacy invocation.
     *
     * @param list<string> $argv
     */
    public static function deprecationMessage(array $argv): string
    {
        return 'Deprecated: positional arguments are deprecated, use "phpmd analyze <file> --format '
            . $argv[2] . ' --ruleset <ruleset>" instead.' . PHP_EOL;
    }
}
