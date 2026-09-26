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

use PHPMD\Exception\RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class ConfigLoader
{
    /**
     * Order of the top level keys in a configuration converted from XML.
     *
     * @var list<string>
     */
    private const KEY_ORDER = [
        'name',
        'description',
        'php-includepath',
        'paths',
        'suffixes',
        'exclude-pattern',
        'format',
        'minimum-priority',
        'maximum-priority',
        'threads',
        'cache',
        'cache-file',
        'cache-strategy',
        'baseline-file',
        'bootstrap',
        'rules',
    ];

    /** @var list<string> */
    private array $notes = [];

    /**
     * @return array<mixed>
     * @throws RuntimeException
     */
    public function load(string $fileName): array
    {
        $this->notes = [];

        if (!is_file($fileName) || !is_readable($fileName)) {
            throw new RuntimeException("Unable to read '{$fileName}'.");
        }

        $config = match (ConfigFormat::fromFileName($fileName)) {
            ConfigFormat::Xml => $this->loadXml($fileName),
            ConfigFormat::Php => include $fileName,
            ConfigFormat::Yaml => $this->loadYaml($fileName),
            ConfigFormat::Json => json_decode(file_get_contents($fileName) ?: '', true),
        };

        if (!is_array($config)) {
            throw new RuntimeException("Invalid configuration in '{$fileName}'.");
        }

        return $config;
    }

    /**
     * Messages about content of an XML file that could not be carried over by the last {@link load()} call.
     *
     * @return list<string>
     */
    public function getNotes(): array
    {
        return $this->notes;
    }

    /**
     * @throws RuntimeException
     */
    private function loadYaml(string $fileName): mixed
    {
        try {
            return Yaml::parseFile($fileName);
        } catch (ParseException $exception) {
            throw new RuntimeException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @return array<string, mixed>
     * @throws RuntimeException
     */
    private function loadXml(string $fileName): array
    {
        $converter = new XmlConfigConverter();
        $config = $converter->convert($fileName);
        $this->notes = $converter->getNotes();

        $sorted = [];
        foreach (self::KEY_ORDER as $key) {
            if (array_key_exists($key, $config)) {
                $sorted[$key] = $config[$key];
            }
        }

        return $sorted + $config;
    }
}
