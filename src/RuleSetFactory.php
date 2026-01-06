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
use PHPMD\Exception\RuleSetNotFoundException;
use RuntimeException;
use SimpleXMLElement;

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
        $this->location = __DIR__ . '/..';
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
     * @throws RuleSetNotFoundException
     * @throws RuntimeException
     */
    public function createSingleRuleSet(string $ruleSetOrFileName): RuleSet
    {
        $fileName = $this->createRuleSetFileName($ruleSetOrFileName);

        return $this->parseRuleSetNode($fileName);
    }

    /**
     * Lists available rule-set identifiers.
     *
     * @return list<string>
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
        // Hide error messages
        $libxml = libxml_use_internal_errors(true);

        $fileContent = file_get_contents($fileName);
        if ($fileContent === false) {
            throw new RuntimeException('Unable to load ' . $fileName);
        }

        $xml = simplexml_load_string($fileContent);
        if (!$xml) {
            // Reset error handling to previous setting
            libxml_use_internal_errors($libxml);
            $error = libxml_get_last_error();

            throw new RuntimeException($error ? trim($error->message) : 'Unknown error');
        }

        $ruleSet = new RuleSet();
        $ruleSet->setFileName($fileName);
        $ruleSet->setName((string) $xml['name']);

        if ($this->strict) {
            $ruleSet->setStrict();
        }

        foreach ($xml->children() as $node) {
            if ($node->getName() === 'php-includepath') {
                $includePath = (string) $node;

                if (is_dir(dirname($fileName) . DIRECTORY_SEPARATOR . $includePath)) {
                    $includePath = dirname($fileName) . DIRECTORY_SEPARATOR . $includePath;
                    $includePath = realpath($includePath);
                }

                $includePath = get_include_path() . PATH_SEPARATOR . $includePath;
                set_include_path($includePath);
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
     * This method parses a single rule xml node. Bases on the structure of the
     * xml node this method delegates the parsing process to another method in
     * this class.
     *
     * @throws RuleClassNotFoundException
     * @throws RuntimeException
     */
    private function parseRuleNode(RuleSet $ruleSet, SimpleXMLElement $node): void
    {
        $ref = (string) $node['ref'];

        if ($ref === '') {
            $this->parseSingleRuleNode($ruleSet, $node);

            return;
        }

        if (str_ends_with($ref, 'xml')) {
            $this->parseRuleSetReferenceNode($ruleSet, $node);

            return;
        }

        $this->parseRuleReferenceNode($ruleSet, $node);
    }

    /**
     * This method parses a complete rule set that was includes a reference in
     * the currently parsed ruleset.
     *
     * @throws RuntimeException
     */
    private function parseRuleSetReferenceNode(RuleSet $ruleSet, SimpleXMLElement $ruleSetNode): void
    {
        $rules = $this->parseRuleSetReference($ruleSetNode);
        foreach ($rules as $rule) {
            if ($this->isIncluded($rule, $ruleSetNode)) {
                $ruleSet->addRule($rule);
            }
        }
    }

    /**
     * Parses a rule-set xml file referenced by the given rule-set xml element.
     *
     * @throws RuntimeException
     * @since 0.2.3
     */
    private function parseRuleSetReference(SimpleXMLElement $ruleSetNode): RuleSet
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
     * @since 0.2.3
     */
    private function isIncluded(Rule $rule, SimpleXMLElement $ruleSetNode): bool
    {
        foreach ($ruleSetNode->exclude as $exclude) {
            if ($rule->getName() === (string) $exclude['name']) {
                return false;
            }
        }

        return true;
    }

    /**
     * This method will create a single rule instance and add it to the given
     * {@link \PHPMD\RuleSet} object.
     *
     * @throws RuleClassFileNotFoundException
     * @throws RuleClassNotFoundException
     */
    private function parseSingleRuleNode(RuleSet $ruleSet, SimpleXMLElement $ruleNode): void
    {
        $fileName = '';

        $ruleSetFolderPath = dirname($ruleSet->getFileName());

        if (isset($ruleNode['file'])) {
            $file = (string) $ruleNode['file'];
            if (is_readable($file)) {
                $fileName = $file;
            } elseif (is_readable($ruleSetFolderPath . DIRECTORY_SEPARATOR . $file)) {
                $fileName = $ruleSetFolderPath . DIRECTORY_SEPARATOR . $file;
            }
        }

        /** @var class-string<Rule> */
        $className = (string) $ruleNode['class'];

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
        $rule->setName((string) $ruleNode['name']);
        $rule->setMessage((string) $ruleNode['message']);
        $rule->setExternalInfoUrl((string) $ruleNode['externalInfoUrl']);

        $rule->setRuleSetName($ruleSet->getName());

        if (isset($ruleNode['since']) && trim($ruleNode['since']) !== '') {
            $rule->setSince((string) $ruleNode['since']);
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

        if ($rule->getPriority() <= $this->minimumPriority && $rule->getPriority() >= $this->maximumPriority) {
            $ruleSet->addRule($rule);
        }
    }

    /**
     * This method parses a single rule that was included from a different
     * rule-set.
     *
     * @throws RuleSetNotFoundException
     * @throws RuleByNameNotFoundException
     * @throws RuntimeException
     */
    private function parseRuleReferenceNode(RuleSet $ruleSet, SimpleXMLElement $ruleNode): void
    {
        $ref = (string) $ruleNode['ref'];

        $fileName = substr($ref, 0, strpos($ref, '.xml/') + 4);
        $fileName = $this->createRuleSetFileName($fileName);

        $ruleName = substr($ref, strpos($ref, '.xml/') + 5);

        $ruleSetFactory = new self();

        $ruleSetRef = $ruleSetFactory->createSingleRuleSet($fileName);
        $rule = $ruleSetRef->getRuleByName($ruleName);

        if (isset($ruleNode['name']) && trim($ruleNode['name']) !== '') {
            $rule->setName((string) $ruleNode['name']);
        }
        if (isset($ruleNode['message']) && trim($ruleNode['message']) !== '') {
            $rule->setMessage((string) $ruleNode['message']);
        }
        if (isset($ruleNode['externalInfoUrl']) && trim($ruleNode['externalInfoUrl']) !== '') {
            $rule->setExternalInfoUrl((string) $ruleNode['externalInfoUrl']);
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

        if ($rule->getPriority() <= $this->minimumPriority && $rule->getPriority() >= $this->maximumPriority) {
            $ruleSet->addRule($rule);
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
     */
    private function parsePropertiesNode(Rule $rule, SimpleXMLElement $propertiesNode): void
    {
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

        return array_map('implode', array_fill(0, count($filePathParts), DIRECTORY_SEPARATOR), $filePathParts);
    }
}
