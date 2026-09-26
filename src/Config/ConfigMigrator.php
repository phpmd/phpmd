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

/**
 * Upgrades a PHPMD 2 configuration file to the current format.
 */
final class ConfigMigrator
{
    /**
     * Rule classes that were renamed in PHPMD 3.
     *
     * @var array<string, string>
     */
    private const RENAMED_CLASSES = [
        'PHPMD\Rule\Design\LongClass' => 'PHPMD\Rule\Design\ExcessiveClassLength',
        'PHPMD\Rule\Design\LongMethod' => 'PHPMD\Rule\Design\ExcessiveMethodLength',
        'PHPMD\Rule\Design\LongParameterList' => 'PHPMD\Rule\Design\ExcessiveParameterList',
        'PHPMD\Rule\Design\NpathComplexity' => 'PHPMD\Rule\Design\NPathComplexity',
        'PHPMD\Rule\Design\WeightedMethodCount' => 'PHPMD\Rule\Design\ExcessiveClassComplexity',
    ];

    /**
     * Rule names of the built-in rule classes with a threshold in {@link THRESHOLDS}.
     *
     * @var array<string, string>
     */
    private const RULE_CLASSES = [
        'PHPMD\Rule\CyclomaticComplexity' => 'CyclomaticComplexity',
        'PHPMD\Rule\ExcessivePublicCount' => 'ExcessivePublicCount',
        'PHPMD\Rule\Design\CouplingBetweenObjects' => 'CouplingBetweenObjects',
        'PHPMD\Rule\Design\DepthOfInheritance' => 'DepthOfInheritance',
        'PHPMD\Rule\Design\ExcessiveClassComplexity' => 'ExcessiveClassComplexity',
        'PHPMD\Rule\Design\ExcessiveClassLength' => 'ExcessiveClassLength',
        'PHPMD\Rule\Design\ExcessiveMethodLength' => 'ExcessiveMethodLength',
        'PHPMD\Rule\Design\ExcessiveParameterList' => 'ExcessiveParameterList',
        'PHPMD\Rule\Design\NPathComplexity' => 'NPathComplexity',
        'PHPMD\Rule\Design\NumberOfChildren' => 'NumberOfChildren',
        'PHPMD\Rule\Design\TooManyFields' => 'TooManyFields',
        'PHPMD\Rule\Design\TooManyMethods' => 'TooManyMethods',
        'PHPMD\Rule\Design\TooManyPublicMethods' => 'TooManyPublicMethods',
    ];

    /**
     * The PHPMD 2 threshold properties of each rule, which are all named "maximum" now.
     *
     * The flag tells whether PHPMD 2 already reported a value equal to the threshold,
     * where PHPMD 3 only reports values above it.
     *
     * @var array<string, array<string, bool>>
     */
    private const THRESHOLDS = [
        'CyclomaticComplexity' => ['reportLevel' => true],
        'NPathComplexity' => ['minimum' => true],
        'ExcessiveMethodLength' => ['minimum' => true],
        'ExcessiveClassLength' => ['minimum' => true],
        'ExcessiveParameterList' => ['minimum' => true],
        'ExcessivePublicCount' => ['minimum' => true],
        'ExcessiveClassComplexity' => ['maximum' => true],
        'CouplingBetweenObjects' => ['maximum' => true],
        'NumberOfChildren' => ['minimum' => true],
        'DepthOfInheritance' => ['minimum' => true],
        'TooManyFields' => ['maxfields' => false],
        'TooManyMethods' => ['maxmethods' => false],
        'TooManyPublicMethods' => ['maxmethods' => false],
    ];

    /** @var list<string> */
    private array $changes = [];

    /** @var list<string> */
    private array $notes = [];

    private bool $preserveBehavior = false;

    /**
     * Lower the thresholds that PHPMD 2 compared inclusively by one,
     * so that exactly the same values are reported as before.
     */
    public function preserveBehavior(): void
    {
        $this->preserveBehavior = true;
    }

    /**
     * @throws RuntimeException
     */
    public function migrate(string $fileName): ConfigMigration
    {
        $this->changes = [];
        $this->notes = [];

        $loader = new ConfigLoader();
        $config = $loader->load($fileName);
        $this->notes = $loader->getNotes();

        $rules = $config['rules'] ?? [];
        if (is_array($rules)) {
            foreach ($rules as $index => $rule) {
                if (is_array($rule)) {
                    $rules[$index] = $this->migrateRule($rule);
                }
            }
            $config['rules'] = $rules;
        }

        return new ConfigMigration($config, $this->changes, $this->notes);
    }

    /**
     * @param array<mixed> $rule
     * @return array<mixed>
     */
    private function migrateRule(array $rule): array
    {
        $class = $rule['class'] ?? null;
        if (is_string($class) && $class !== '') {
            $rule['class'] = $this->migrateClass($class);
        }

        $this->checkReference($rule);

        $ruleName = $this->resolveRuleName($rule);
        $properties = $rule['properties'] ?? null;
        if ($ruleName !== null && is_array($properties) && isset(self::THRESHOLDS[$ruleName])) {
            $rule['properties'] = $this->migrateThreshold($ruleName, $properties);
        }

        return $rule;
    }

    private function migrateClass(string $class): string
    {
        $newClass = ltrim($class, '\\');
        // PHPMD 1 class names, which were kept as aliases until 2.9
        if (str_starts_with($newClass, 'PHP_PMD_')) {
            $newClass = 'PHPMD\\' . str_replace('_', '\\', substr($newClass, 8));
        }
        $newClass = self::RENAMED_CLASSES[$newClass] ?? $newClass;

        if ($newClass !== ltrim($class, '\\')) {
            $this->changes[] = "Renamed rule class {$class} to {$newClass}.";
        }

        return $newClass;
    }

    /**
     * @param array<mixed> $rule
     */
    private function checkReference(array $rule): void
    {
        $ref = $rule['ref'] ?? null;
        if (!is_string($ref) || !preg_match('`^(?<file>.*\.(?:xml|ya?ml|json|php))(?:/.*)?$`i', $ref, $match)) {
            return;
        }

        $bundled = preg_match('`^(?:rulesets/)?(?<name>[\w-]+)\.xml$`', $match['file'], $bundledMatch)
            && is_file(__DIR__ . '/../../rulesets/' . $bundledMatch['name'] . '.xml');
        if ($bundled) {
            return;
        }

        $this->notes[] = "The referenced rule set {$match['file']} was not migrated, run the migration on it as well to do so.";
    }

    /**
     * @param array<mixed> $rule
     */
    private function resolveRuleName(array $rule): ?string
    {
        $ref = $rule['ref'] ?? null;
        if (is_string($ref) && $ref !== '') {
            // A reference to a whole rule set has no properties of its own
            if (preg_match('`\.(?:xml|ya?ml|json|php)$`i', $ref)) {
                return null;
            }

            return preg_replace('`^.*\.(?:xml|ya?ml|json|php)/`i', '', $ref);
        }

        $class = $rule['class'] ?? null;
        if (is_string($class) && $class !== '') {
            return self::RULE_CLASSES[$class] ?? null;
        }

        // Without ref or class, the entry modifies a rule loaded by an earlier entry
        $name = $rule['name'] ?? null;

        return is_string($name) ? $name : null;
    }

    /**
     * @param array<mixed> $properties
     * @return array<mixed>
     */
    private function migrateThreshold(string $ruleName, array $properties): array
    {
        foreach (self::THRESHOLDS[$ruleName] as $oldName => $inclusive) {
            if (!array_key_exists($oldName, $properties)) {
                continue;
            }

            if ($oldName !== 'maximum' && array_key_exists('maximum', $properties)) {
                unset($properties[$oldName]);
                $this->changes[] = "Removed property {$oldName} of rule {$ruleName}, maximum is already set.";

                continue;
            }
            if ($oldName !== 'maximum') {
                $properties = $this->renameKey($properties, $oldName, 'maximum');
                $this->changes[] = "Renamed property {$oldName} of rule {$ruleName} to maximum.";
            }

            if ($inclusive && $this->preserveBehavior) {
                $properties['maximum'] = $this->lowerThreshold($ruleName, $properties['maximum']);
            }
        }

        return $properties;
    }

    /**
     * @param array<mixed> $array
     * @return array<mixed>
     */
    private function renameKey(array $array, string $oldKey, string $newKey): array
    {
        $renamed = [];
        foreach ($array as $key => $value) {
            $renamed[$key === $oldKey ? $newKey : $key] = $value;
        }

        return $renamed;
    }

    private function lowerThreshold(string $ruleName, mixed $value): mixed
    {
        if (!is_int($value) && !(is_string($value) && ctype_digit($value))) {
            $this->notes[] = "Could not adjust the non-numeric maximum of rule {$ruleName}.";

            return $value;
        }

        $newValue = (int) $value - 1;
        $this->changes[] = "Lowered maximum of rule {$ruleName} from {$value} to {$newValue} to keep "
            . 'reporting values equal to the old threshold.';

        return $newValue;
    }
}
