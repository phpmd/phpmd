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

use PHPMD\Exception\RuleClassFileNotFoundException;
use PHPMD\Exception\RuleClassNotFoundException;
use PHPMD\Exception\RuleNotFoundException;
use PHPMD\Exception\RuleSetNotFoundException;
use RuntimeException;
use SimpleXMLElement;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * This factory class is used to create the {@link \PHPMD\RuleSet} instance
 * that PHPMD will use to analyze the source code.
 */
class RuleSetFactory
{
    /**
     * Is the strict mode active?
     *
     * @since 1.2.0
     */
    private bool $strict = false;

    /** The data directory set within the class constructor. */
    private readonly string $location;

    /** The minimum priority for rules to load. */
    private int $minimumPriority = Rule::LOWEST_PRIORITY;

    /** The maximum priority for rules to load. */
    private int $maximumPriority = Rule::HIGHEST_PRIORITY;

    /**
     * Constructs a new default rule-set factory instance.
     */
    public function __construct()
    {
        $this->location = __DIR__ . '/../../resources';
    }

    /**
     * Activates the strict mode for all rule sets.
     *
     * @since 1.2.0
     */
    public function setStrict(): void
    {
        $this->strict = true;
    }

    /**
     * Sets the minimum priority that a rule must have.
     *
     * @param int $minimumPriority The minimum priority value.
     */
    public function setMinimumPriority(int $minimumPriority): void
    {
        $this->minimumPriority = $minimumPriority;
    }

    /**
     * Sets the maximum priority that a rule must have.
     *
     * @param int $maximumPriority The maximum priority value.
     */
    public function setMaximumPriority(int $maximumPriority): void
    {
        $this->maximumPriority = $maximumPriority;
    }

    /**
     * Creates an array of rule-set instances for the given argument.
     *
     * @param string $ruleSetFileNames Comma-separated string of rule-set filenames or identifier.
     * @return list<RuleSet>
     * @throws RuntimeException
     */
    public function createRuleSets(string $ruleSetFileNames): array
    {
        $ruleSets = [];

        $ruleSetFileName = strtok($ruleSetFileNames, ',');
        while ($ruleSetFileName) {
            $ruleSets[] = $this->createSingleRuleSet($ruleSetFileName);

            $ruleSetFileName = strtok(',');
        }

        return $ruleSets;
    }

    /**
     * Creates a single rule-set instance for the given filename or identifier.
     *
     * @param string $ruleSetOrFileName The rule-set filename or identifier.
     */
    public function createSingleRuleSet(string $ruleSetOrFileName): RuleSet
    {
        $fileName = $this->createRuleSetFileName($ruleSetOrFileName);

        return $this->parseRuleSetNode($fileName);
    }

    /**
     * Lists available rule-set identifiers.
     *
     * @return string[]
     */
    public function listAvailableRuleSets(): array
    {
        return [
            ...self::listRuleSetsInDirectory($this->location . '/rulesets/'),
            ...self::listRuleSetsInDirectory(getcwd() . '/rulesets/'),
        ];
    }

    /**
     * This method creates the filename for a rule-set identifier or it returns
     * the input when it is already a filename.
     *
     * @param string $ruleSetOrFileName The rule-set filename or identifier.
     * @return string Path to rule set file name
     * @throws RuleSetNotFoundException Thrown if no readable file found
     */
    private function createRuleSetFileName(string $ruleSetOrFileName): string
    {
        foreach ($this->filePaths($ruleSetOrFileName) as $filePath) {
            if ($this->isReadableFile($filePath)) {
                return $filePath;
            }
        }

        throw new RuleSetNotFoundException($ruleSetOrFileName);
    }

    /**
     * Lists available rule-set identifiers in given directory.
     *
     * @param string $directory The directory to scan for rule-sets.
     * @return list<string>
     */
    private static function listRuleSetsInDirectory(string $directory): array
    {
        $ruleSets = [];
        if (is_dir($directory)) {
            $filesPaths = scandir($directory) ?: [];
            foreach ($filesPaths as $file) {
                $matches = [];
                if (is_file($directory . $file) && preg_match('/^(.*)\.xml$/', $file, $matches)) {
                    $ruleSets[] = $matches[1];
                }
            }
        }

        return $ruleSets;
    }

    /**
     * This method parses the rule-set definition in the given file.
     *
     * @throws RuntimeException When loading the XML file fails.
     */
    private function parseRuleSetNode(string $fileName): RuleSet
    {
        $format = preg_match('/\.(?<format>php|json|ya?ml)(?:\.dist)?$/i', $fileName, $match)
            ? strtolower($match['format'])
            : 'xml';

        return match ($format) {
            'php' => $this->getConfigFromPhpFile($fileName),
            'yml', 'yaml' => $this->getConfigFromYamlFile($fileName),
            'json' => $this->getConfigFromJsonFile($fileName),
            default => $this->getConfigFromXmlFile($fileName),
        };
    }

    /**
     * This method parses a single rule xml node. Bases on the structure of the
     * xml node this method delegates the parsing process to another method in
     * this class.
     *
     * @param array<string, mixed>|ArrayAccess<string, mixed>|SimpleXMLElement $node
     * @throws RuleClassNotFoundException
     * @throws RuntimeException
     */
    private function parseRuleNode(RuleSet $ruleSet, array|ArrayAccess|SimpleXMLElement $node): void
    {
        $ref = (string) ($node['ref'] ?? '');

        if ($ref === '') {
            $this->parseSingleRuleNode($ruleSet, $node);

            return;
        }

        if (preg_match('/\.(?:xml|ya?ml|php)$/i', $ref)) {
            $this->parseRuleSetReferenceNode($ruleSet, $node);

            return;
        }

        $this->parseRuleReferenceNode($ruleSet, $node, $ref);
    }

    /**
     * This method parses a complete rule set that was includes a reference in
     * the currently parsed ruleset.
     *
     * @param array<string, mixed>|ArrayAccess<string, mixed>|SimpleXMLElement $ruleSetNode
     * @throws RuntimeException
     */
    private function parseRuleSetReferenceNode(
        RuleSet $ruleSet,
        array|ArrayAccess|SimpleXMLElement $ruleSetNode,
    ): void {
        foreach ($this->parseRuleSetReference($ruleSetNode) as $rule) {
            if ($this->isIncluded($rule, $ruleSetNode)) {
                $ruleSet->addRule($rule);
            }
        }
    }

    /**
     * Parses a rule-set xml file referenced by the given rule-set xml element.
     *
     * @param array<string, mixed>|ArrayAccess<string, mixed>|SimpleXMLElement $ruleSetNode
     * @throws RuntimeException
     * @since 0.2.3
     */
    private function parseRuleSetReference(array|ArrayAccess|SimpleXMLElement $ruleSetNode): RuleSet
    {
        $ruleSetFactory = new self();
        $ruleSetFactory->setMinimumPriority($this->minimumPriority);
        $ruleSetFactory->setMaximumPriority($this->maximumPriority);

        return $ruleSetFactory->createSingleRuleSet((string) $ruleSetNode['ref']);
    }

    /**
     * Checks if the given rule is included/not excluded by the given rule-set
     * reference node.
     *
     * @param array<string, mixed>|ArrayAccess<string, mixed>|SimpleXMLElement $ruleSetNode
     * @since 0.2.3
     */
    private function isIncluded(Rule $rule, array|ArrayAccess|SimpleXMLElement $ruleSetNode): bool
    {
        $excludes = (is_object($ruleSetNode) ? ($ruleSetNode->exclude ?? null) : null)
            ?? $ruleSetNode['exclude']
            ?? [];

        foreach ($excludes as $exclude) {
            $name = is_string($exclude) ? $exclude : (string) ($exclude['name'] ?? '');

            if ($rule->getName() === $name) {
                return false;
            }
        }

        return true;
    }

    /**
     * This method will create a single rule instance and add it to the given
     * {@link \PHPMD\RuleSet} object.
     *
     * @param array<string, mixed>|ArrayAccess<string, mixed>|SimpleXMLElement $ruleNode
     * @throws RuleClassFileNotFoundException
     * @throws RuleClassNotFoundException
     */
    private function parseSingleRuleNode(RuleSet $ruleSet, array|ArrayAccess|SimpleXMLElement $ruleNode): void
    {
        $fileName = '';

        $ruleSetFolderPath = dirname($ruleSet->getFileName());

        if (isset($ruleNode['file'])) {
            $ruleNodeFile = (string )$ruleNode['file'];

            if (is_readable($ruleNodeFile)) {
                $fileName = $ruleNodeFile;
            } elseif (is_readable($ruleSetFolderPath . DIRECTORY_SEPARATOR . $ruleNodeFile)) {
                $fileName = $ruleSetFolderPath . DIRECTORY_SEPARATOR . $ruleNodeFile;
            }
        }

        /** @var class-string<Rule> */
        $className = (string) (
            $ruleNode['class']
            ?? ($fileName === '' ? '' : pathinfo($fileName, PATHINFO_FILENAME))
        );

        if (!is_readable($fileName)) {
            $fileName = strtr($className, '\\', '/') . '.php';
        }

        if (!is_readable($fileName)) {
            $fileName = str_replace(['\\', '_'], '/', $className) . '.php';
        }

        if (!class_exists($className)) {
            $handle = @fopen($fileName, 'rb', true);
            if (!$handle) {
                throw new RuleClassFileNotFoundException($className);
            }
            fclose($handle);

            include_once $fileName;

            if (!class_exists($className)) {
                throw new RuleClassNotFoundException($className);
            }
        }

        $rule = new $className();
        $this->withNonEmptyStringAtKey($ruleNode, 'name', $rule->setName(...));
        $this->withNonEmptyStringAtKey($ruleNode, 'message', $rule->setMessage(...));
        $this->withNonEmptyStringAtKey($ruleNode, 'externalInfoUrl', $rule->setExternalInfoUrl(...));

        $rule->setRuleSetName($ruleSet->getName());

        $this->withNonEmptyStringAtKey($ruleNode, 'since', [$rule, 'setSince']);

        $this->parseRuleProperties($rule, $ruleNode);

        if ($rule->getPriority() <= $this->minimumPriority && $rule->getPriority() >= $this->maximumPriority) {
            $ruleSet->addRule($rule);
        }
    }

    /**
     * This method parses a single rule that was included from a different
     * rule-set.
     *
     * @param array<string, mixed>|ArrayAccess<string, mixed>|SimpleXMLElement $ruleNode
     * @throws RuleSetNotFoundException
     * @throws RuleByNameNotFoundException
     * @throws RuntimeException
     */
    private function parseRuleReferenceNode(
        RuleSet $ruleSet,
        array|ArrayAccess|SimpleXMLElement $ruleNode,
        string $ref,
    ): void {
        [
            'file' => $fileName,
            'rule' => $ruleName,
        ] = preg_match('`^(?<file>.*\.(?:xml|ya?ml|php))/(?<rule>.*)`i', $ref, $matches)
            ? $matches
            : ['file' => '', 'rule' => $ref];

        $ruleSetRef = $fileName === ''
            ? $this->findFileForRule($ruleName)
            : $this->createSingleRuleSet($this->createRuleSetFileName($fileName));

        $rule = $ruleSetRef->getRuleByName($ruleName);

        $this->withNonEmptyStringAtKey($ruleNode, 'name', $rule->setName(...));
        $this->withNonEmptyStringAtKey($ruleNode, 'message', $rule->setMessage(...));
        $this->withNonEmptyStringAtKey($ruleNode, 'externalInfoUrl', $rule->setExternalInfoUrl(...));

        $this->parseRuleProperties($rule, $ruleNode);

        if ($rule->getPriority() <= $this->minimumPriority && $rule->getPriority() >= $this->maximumPriority) {
            $ruleSet->addRule($rule);
        }
    }

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed>|SimpleXMLElement $config
     */
    private function withNonEmptyStringAtKey(
        array|ArrayAccess|SimpleXMLElement $config,
        string $key,
        callable $setter,
    ): void {
        $value = trim((string) ($config[$key] ?? ''));

        if ($value !== '') {
            $setter($value);
        }
    }

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed>|SimpleXMLElement $ruleNode
     */
    private function parseRuleProperties(Rule $rule, array|ArrayAccess|SimpleXMLElement $ruleNode): void
    {
        $this->withNonEmptyStringAtKey($ruleNode, 'description', [$rule, 'setDescription']);
        $this->withNonEmptyStringAtKey($ruleNode, 'example', [$rule, 'addExample']);

        if (isset($ruleNode['priority'])) {
            $rule->setPriority((int) $ruleNode['priority']);
        }

        if (isset($ruleNode['properties'])) {
            $this->parsePropertiesNode($rule, $ruleNode['properties']);
        }

        if (!($ruleNode instanceof \SimpleXMLElement)) {
            return;
        }

        foreach ($ruleNode->children() as $node) {
            if ($node->getName() === 'description') {
                $rule->setDescription((string) $node);
            } elseif ($node->getName() === 'example') {
                $rule->addExample((string) $node);
            } elseif ($node->getName() === 'priority') {
                $rule->setPriority((int) $node);
            } elseif ($node->getName() === 'properties') {
                $this->parsePropertiesNode($rule, $node);
            }
        }
    }

    /**
     * This method parses a xml properties structure and adds all found properties
     * to the given <b>$rule</b> object.
     *
     * <code>
     *   ...
     *   <properties>
     *       <property name="foo" value="42" />
     *       <property name="bar" value="23" />
     *       ...
     *   </properties>
     *   ...
     * </code>
     *
     * @param array<string, mixed>|SimpleXMLElement $propertiesNode
     */
    private function parsePropertiesNode(Rule $rule, array|SimpleXMLElement $propertiesNode): void
    {
        if (!($propertiesNode instanceof \SimpleXMLElement)) {
            foreach ($propertiesNode as $name => $value) {
                $name = trim($name);

                if ($name !== '') {
                    $rule->addProperty($name, $value);
                }
            }

            return;
        }

        foreach ($propertiesNode->children() as $node) {
            if ($node->getName() === 'property') {
                $this->addProperty($rule, $node);
            }
        }
    }

    /**
     * Adds an additional property to the given <b>$rule</b> instance.
     */
    private function addProperty(Rule $rule, SimpleXMLElement $node): void
    {
        $name = trim((string) $node['name']);
        $value = trim($this->getPropertyValue($node));
        if ($name !== '' && $value !== '') {
            $rule->addProperty($name, $value);
        }
    }

    /**
     * Returns the value of a property node. This value can be expressed in
     * two different notations. First version is an attribute named <b>value</b>
     * and the second valid notation is a child element named <b>value</b> that
     * contains the value as character data.
     *
     * @since 0.2.5
     */
    private function getPropertyValue(SimpleXMLElement $propertyNode): string
    {
        if (isset($propertyNode->value)) {
            return (string) $propertyNode->value;
        }

        return (string) $propertyNode['value'];
    }

    /**
     * Returns an array of path exclude patterns in format described at
     *
     * http://pmd.sourceforge.net/pmd-5.0.4/howtomakearuleset.html#Excluding_files_from_a_ruleset
     *
     * @param string $fileName The filename of a rule-set definition.
     * @return list<string>
     * @throws RuntimeException Thrown if file is not proper xml
     */
    public function getIgnorePattern(string $fileName): array
    {
        $excludes = [];
        $files = array_map(trim(...), explode(',', $fileName));
        $files = array_filter($files);

        foreach ($files as $ruleSetFileName) {
            $ruleSetFileName = $this->createRuleSetFileName($ruleSetFileName);

            // Hide error messages
            $libxml = libxml_use_internal_errors(true);
            $fileContent = file_get_contents($ruleSetFileName);
            if ($fileContent === false) {
                throw new RuntimeException('Unable to load ' . $ruleSetFileName);
            }

            $xml = simplexml_load_string($fileContent);
            if (!$xml) {
                // Reset error handling to previous setting
                libxml_use_internal_errors($libxml);
                $error = libxml_get_last_error();

                throw new RuntimeException($error ? trim($error->message) : 'Unknown error');
            }

            foreach ($xml->children() as $node) {
                if ($node->getName() === 'exclude-pattern') {
                    $excludes[] = '' . $node;
                }
            }

            return $excludes;
        }

        return [];
    }

    /**
     * Checks if given file path exists, is file (or symlink to file)
     * and is readable by current user
     *
     * @param string $filePath File path to check against
     * @return bool True if file exists and is readable, false otherwise
     */
    private function isReadableFile(string $filePath): bool
    {
        return is_readable($filePath) && is_file($filePath);
    }

    /**
     * Returns list of possible file paths to search against code rules
     *
     * @param string $fileName Rule set file name
     * @return list<string> Array of possible file locations
     */
    private function filePaths(string $fileName): array
    {
        $filePathParts = [
            [$fileName],
            [$this->location, $fileName],
            [$this->location, 'rulesets', $fileName . '.xml'],
            [getcwd(), 'rulesets', $fileName . '.xml'],
        ];

        foreach (explode(PATH_SEPARATOR, get_include_path()) as $includePath) {
            $filePathParts[] = [$includePath, $fileName];
            $filePathParts[] = [$includePath, $fileName . '.xml'];
        }

        return array_map('implode', array_fill(0, \count($filePathParts), DIRECTORY_SEPARATOR), $filePathParts);
    }

    /**
     * Load rule-set config from a .php file.
     *
     * @throws RuntimeException
     */
    private function getConfigFromPhpFile(string $fileName): RuleSet
    {
        return $this->getConfigFromArray($fileName, include $fileName);
    }

    /**
     * Load rule-set config from a .yaml file.
     *
     * @throws ParseException
     * @throws RuntimeException
     */
    private function getConfigFromYamlFile(string $fileName): RuleSet
    {
        return $this->getConfigFromArray($fileName, Yaml::parseFile($fileName));
    }

    /**
     * Load rule-set config from a .json file.
     *
     * @throws RuntimeException
     */
    private function getConfigFromJsonFile(string $fileName): RuleSet
    {
        return $this->getConfigFromArray($fileName, json_decode(file_get_contents($fileName)));
    }

    /**
     * Load rule-set config from filename and array.
     *
     * @param array<string, mixed> $config
     * @throws RuntimeException
     */
    private function getConfigFromArray(string $fileName, array $config): RuleSet
    {
        $ruleSet = $this->initRuleSet($fileName, $config['name'] ?? null);
        $this->configRuleSetWith($ruleSet, $config);

        return $ruleSet;
    }

    /**
     * Load rule-set config from a .xml file.
     *
     * @throws RuntimeException
     */
    private function getConfigFromXmlFile(string $fileName): RuleSet
    {
        // Hide error messages
        $libxml = libxml_use_internal_errors(true);

        $xml = simplexml_load_string(file_get_contents($fileName));
        if ($xml === false) {
            // Reset error handling to previous setting
            libxml_use_internal_errors($libxml);

            throw new RuntimeException(trim(libxml_get_last_error()->message));
        }

        $ruleSet = $this->initRuleSet($fileName, $xml['name'] ?? null);

        foreach ($xml->children() as $node) {
            if ($node->getName() === 'php-includepath') {
                $this->addIncludePath($fileName, (string) $node);
            }
        }

        foreach ($xml->children() as $node) {
            if ($node->getName() === 'description') {
                $ruleSet->setDescription((string) $node);
            } elseif ($node->getName() === 'rule') {
                $this->parseRuleNode($ruleSet, $node);
            }
        }

        return $ruleSet;
    }

    /**
     * Configure RuleSet according to given array config.
     *
     * @param array<string, mixed> $config
     * @throws RuntimeException
     */
    private function configRuleSetWith(RuleSet $ruleSet, array $config): void
    {
        $ruleSet->setDescription((string) ($config['description'] ?? ''));

        foreach ((array) ($config['php-includepath'] ?? []) as $value) {
            $this->addIncludePath($ruleSet->getFileName(), (string) $value);
        }

        foreach ((array) ($config['rules'] ?? []) as $rule) {
            $this->parseRuleNode($ruleSet, $rule);
        }
    }

    /**
     * Create a RuleSet with initial properties: filename, name (inferred from filename if null)
     * and propagate the RuleSetFactory strict state into this new RuleSet.
     */
    private function initRuleSet(string $fileName, mixed $name): RuleSet
    {
        $ruleSet = new RuleSet();
        $ruleSet->setFileName($fileName);
        $ruleSet->setName((string) ($name ?? pathinfo($fileName, PATHINFO_FILENAME)));

        if ($this->strict) {
            $ruleSet->setStrict();
        }

        return $ruleSet;
    }

    /**
     * Add given path to PHP include paths.
     */
    private function addIncludePath(string $fileName, string $includePath): void
    {
        $directory = dirname($fileName) . DIRECTORY_SEPARATOR . $includePath;

        if (is_dir($directory)) {
            $includePath = realpath($directory);
        }

        set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);
    }

    /**
     * Return the first file in the internal ruleset having a given rule name.
     *
     * @throws RuleNotFoundException
     * @throws RuleSetNotFoundException
     * @throws RuntimeException
     */
    private function findFileForRule(string $ruleName): RuleSet
    {
        foreach (InternalRuleSet::getNames() as $setName) {
            $ruleSet = $this->createSingleRuleSet($this->createRuleSetFileName($setName));

            foreach ($ruleSet->getRules() as $rule) {
                if ($rule->getName() === $ruleName) {
                    return $ruleSet;
                }
            }
        }

        throw new RuleNotFoundException($ruleName);
    }
}
