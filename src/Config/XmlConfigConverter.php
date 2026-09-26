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
use SimpleXMLElement;

final class XmlConfigConverter
{
    /**
     * Top level options that hold a single value.
     *
     * @var list<string>
     */
    private const SCALAR_OPTIONS = [
        'format',
        'cache',
        'cache-file',
        'cache-strategy',
        'baseline-file',
        'bootstrap',
        'minimum-priority',
        'maximum-priority',
        'threads',
    ];

    /**
     * Top level options that can be repeated.
     *
     * @var list<string>
     */
    private const LIST_OPTIONS = ['php-includepath', 'paths', 'exclude-pattern', 'suffixes'];

    /**
     * Rule attributes that are copied as they are.
     *
     * @var list<string>
     */
    private const RULE_ATTRIBUTES = ['ref', 'name', 'class', 'file', 'since', 'message', 'externalInfoUrl'];

    /** @var list<string> */
    private array $notes = [];

    /**
     * @return array<string, mixed>
     * @throws RuntimeException
     */
    public function convert(string $fileName): array
    {
        $this->notes = [];

        $xml = $this->load($fileName);

        $config = [];
        if (isset($xml['name'])) {
            $config['name'] = (string) $xml['name'];
        }

        foreach ($xml->children() as $node) {
            $config = $this->addRuleSetChild($config, $node);
        }

        return $config;
    }

    /**
     * Messages about content that could not be carried over by the last {@link convert()} call.
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
    private function load(string $fileName): SimpleXMLElement
    {
        $libxml = libxml_use_internal_errors(true);
        $content = file_get_contents($fileName);
        $xml = $content ? simplexml_load_string($content) : false;
        $error = libxml_get_last_error();
        libxml_clear_errors();
        libxml_use_internal_errors($libxml);

        if ($xml === false) {
            throw new RuntimeException(
                "Unable to parse '{$fileName}'" . ($error ? ': ' . trim($error->message) : '.')
            );
        }

        return $xml;
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function addRuleSetChild(array $config, SimpleXMLElement $node): array
    {
        $nodeName = $node->getName();
        if ($nodeName === 'description') {
            return [...$config, 'description' => $this->dedent((string) $node)];
        }
        if ($nodeName === 'rule') {
            return [...$config, 'rules' => [...(array) ($config['rules'] ?? []), $this->convertRule($node)]];
        }
        if (in_array($nodeName, self::LIST_OPTIONS, true)) {
            return [...$config, $nodeName => [...(array) ($config[$nodeName] ?? []), trim((string) $node)]];
        }
        // Only the first occurrence of a single value option is read
        if (in_array($nodeName, self::SCALAR_OPTIONS, true)) {
            return $config + [$nodeName => $this->convertScalar($nodeName, trim((string) $node))];
        }

        $this->notes[] = "Dropped unsupported element <{$nodeName}>.";

        return $config;
    }

    /**
     * @return array<string, mixed>
     */
    private function convertRule(SimpleXMLElement $node): array
    {
        $rule = [];
        foreach (self::RULE_ATTRIBUTES as $attribute) {
            if (isset($node[$attribute]) && trim((string) $node[$attribute]) !== '') {
                $rule[$attribute] = trim((string) $node[$attribute]);
            }
        }
        if (isset($node['priority'])) {
            $rule['priority'] = $this->convertNumber(trim((string) $node['priority']));
        }

        foreach ($node->children() as $child) {
            $rule = $this->addRuleChild($rule, $child);
        }

        if (($rule['properties'] ?? null) === []) {
            unset($rule['properties']);
        }

        return $rule;
    }

    /**
     * @param array<string, mixed> $rule
     * @return array<string, mixed>
     */
    private function addRuleChild(array $rule, SimpleXMLElement $child): array
    {
        $childName = $child->getName();
        if ($childName === 'priority') {
            return [...$rule, 'priority' => $this->convertNumber(trim((string) $child))];
        }
        if ($childName === 'description' || $childName === 'example') {
            return $this->addText($rule, $childName, (string) $child);
        }
        if ($childName === 'properties') {
            // A later value wins when XML sets a property twice, "+" also keeps numeric names intact
            $properties = $this->convertProperties($child) + (array) ($rule['properties'] ?? []);

            return [...$rule, 'properties' => $properties];
        }
        if ($childName === 'exclude') {
            return [...$rule, 'exclude' => [...(array) ($rule['exclude'] ?? []), trim((string) $child['name'])]];
        }

        $this->notes[] = "Dropped unsupported element <{$childName}> of rule " . $this->describe($rule) . '.';

        return $rule;
    }

    /**
     * @param array<string, mixed> $rule
     * @return array<string, mixed>
     */
    private function addText(array $rule, string $key, string $text): array
    {
        $text = $this->dedent($text);
        if ($text === '') {
            return $rule;
        }
        if (isset($rule[$key])) {
            $this->notes[] = "Dropped additional <{$key}> of rule " . $this->describe($rule)
                . ', only one is supported.';

            return $rule;
        }

        return [...$rule, $key => $text];
    }

    /**
     * @return array<string, int|string>
     */
    private function convertProperties(SimpleXMLElement $node): array
    {
        $properties = [];
        foreach ($node->children() as $property) {
            if ($property->getName() !== 'property') {
                continue;
            }

            $name = trim((string) $property['name']);
            $value = trim(isset($property->value) ? (string) $property->value : (string) $property['value']);
            // Empty values are ignored to keep the same result
            if ($name !== '' && $value !== '') {
                $properties[$name] = $this->convertNumber($value);
            }
        }

        return $properties;
    }

    private function convertScalar(string $option, string $value): bool|int|string
    {
        if ($option === 'cache') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
        }

        return $this->convertNumber($value);
    }

    private function convertNumber(string $value): int|string
    {
        return ctype_digit($value) && ($value === '0' || $value[0] !== '0') ? (int) $value : $value;
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function describe(array $rule): string
    {
        $identifier = $rule['name'] ?? $rule['ref'] ?? $rule['class'] ?? '';

        return is_string($identifier) && $identifier !== '' ? "'{$identifier}'" : '(unnamed)';
    }

    /**
     * Trims the text and removes the indentation shared by all of its lines.
     */
    private function dedent(string $text): string
    {
        $lines = explode("\n", trim(str_replace("\r\n", "\n", $text), "\n"));
        $indent = null;
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $lineIndent = strlen($line) - strlen(ltrim($line));
            $indent = $indent === null ? $lineIndent : min($indent, $lineIndent);
        }

        $lines = array_map(
            static fn(string $line): string => rtrim(substr($line, min($indent ?? 0, strlen($line)))),
            $lines
        );

        return trim(implode("\n", $lines));
    }
}
