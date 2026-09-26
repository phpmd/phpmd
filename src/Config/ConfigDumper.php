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

use PHPMD\Exception\InvalidArgumentException;
use PHPMD\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * Serializes a configuration array into one of the array based file formats.
 */
final class ConfigDumper
{
    private const DOCUMENTATION_URL = 'https://phpmd.org/documentation/creating-a-ruleset.html';

    /**
     * @param array<mixed> $config
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dump(array $config, ConfigFormat $format): string
    {
        return match ($format) {
            ConfigFormat::Yaml => $this->dumpYaml($config),
            ConfigFormat::Json => $this->dumpJson($config),
            default => throw new InvalidArgumentException("Writing {$format->value} files is not supported."),
        };
    }

    /**
     * @param array<mixed> $config
     * @throws RuntimeException
     */
    private function dumpJson(array $config): string
    {
        $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException('Unable to encode the configuration as JSON: ' . json_last_error_msg());
        }

        return $json . "\n";
    }

    /**
     * @param array<mixed> $config
     */
    private function dumpYaml(array $config): string
    {
        $flags = Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK;
        // Writes "- ref: ..." instead of a lone "-" followed by the mapping, available since symfony/yaml 7.3
        if (defined(Yaml::class . '::DUMP_COMPACT_NESTED_MAPPING')) {
            $flags |= Yaml::DUMP_COMPACT_NESTED_MAPPING;
        }

        return '# PHPMD configuration, see ' . self::DOCUMENTATION_URL . "\n"
            . Yaml::dump($config, 10, 2, $flags);
    }
}
