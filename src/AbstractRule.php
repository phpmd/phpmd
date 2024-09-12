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

use InvalidArgumentException;
use OutOfBoundsException;
use PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException;
use PDepend\Source\AST\ASTNode;
use PHPMD\Node\AbstractTypeNode;
use PHPMD\Node\ClassNode;
use PHPMD\Node\EnumNode;
use PHPMD\Node\InterfaceNode;
use PHPMD\Node\NodeInfoFactory;
use PHPMD\Node\TraitNode;
use RuntimeException;

/**
 * This is the abstract base class for PHPMD rules.
 *
 * @SuppressWarnings(PHPMD)
 */
abstract class AbstractRule implements Rule
{
    /** The name for this rule instance. */
    private string $name = '';

    /** The violation message text for this rule. */
    private string $message = '';

    /** The version since when this rule is available. */
    private ?string $since = null;

    /** An url will external information for this rule. */
    private string $externalInfoUrl = '';

    /** An optional description for this rule. */
    private string $description = '';

    /**
     * A list of code examples for this rule.
     *
     * @var list<string>
     */
    private array $examples = [];

    /** The name of the parent rule-set instance. */
    private string $ruleSetName = '';

    /** The priority of this rule. */
    private int $priority = self::LOWEST_PRIORITY;

    /**
     * Configuration properties for this rule instance.
     *
     * @var array<string, string>
     */
    private array $properties = [];

    /** The report for object for this rule. */
    private Report $report;

    /** Should this rule force the strict mode. */
    private bool $strict = false;

    /**
     * Returns the name for this rule instance.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name for this rule instance.
     *
     * @param string $name The rule name.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the version since when this rule is available or <b>null</b>.
     */
    public function getSince(): ?string
    {
        return $this->since;
    }

    /**
     * Sets the version since when this rule is available.
     *
     * @param string $since The version number.
     */
    public function setSince(?string $since): void
    {
        $this->since = $since;
    }

    /**
     * Returns the violation message text for this rule.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Sets the violation message text for this rule.
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Returns an url will external information for this rule.
     */
    public function getExternalInfoUrl(): string
    {
        return $this->externalInfoUrl;
    }

    /**
     * Sets an url will external information for this rule.
     *
     * @param string $externalInfoUrl The info url.
     */
    public function setExternalInfoUrl(string $externalInfoUrl): void
    {
        $this->externalInfoUrl = $externalInfoUrl;
    }

    /**
     * Returns the description text for this rule instance.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description text for this rule instance.
     *
     * @param string $description The description text.
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns a list of examples for this rule.
     *
     * @return list<string>
     */
    public function getExamples(): array
    {
        return $this->examples;
    }

    /**
     * Adds a code example for this rule.
     *
     * @param string $example The code example.
     */
    public function addExample(string $example): void
    {
        $this->examples[] = $example;
    }

    /**
     * Returns the priority of this rule.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Set the priority of this rule.
     *
     * @param int $priority The rule priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Returns the name of the parent rule-set instance.
     */
    public function getRuleSetName(): string
    {
        return $this->ruleSetName;
    }

    /**
     * Sets the name of the parent rule set instance.
     *
     * @param string $ruleSetName The rule-set name.
     */
    public function setRuleSetName(string $ruleSetName): void
    {
        $this->ruleSetName = $ruleSetName;
    }

    /**
     * Returns the violation report for this rule.
     */
    public function getReport(): Report
    {
        return $this->report;
    }

    /**
     * Sets the violation report for this rule.
     */
    public function setReport(Report $report): void
    {
        $this->report = $report;
    }

    /**
     * Adds a configuration property to this rule instance.
     */
    public function addProperty(string $name, string $value): void
    {
        $this->properties[$name] = $value;
    }

    /**
     * Returns the value of a configured property
     *
     * Throws an exception when no property with <b>$name</b> exists
     * and no default value to fall back was given.
     *
     * @template T
     * @param string $name The name of the property, e.g. "ignore-whitespace".
     * @param ?T $default An optional default value to fall back instead of throwing an exception.
     * @return string|T The value of a configured property.
     * @throws OutOfBoundsException When no property for <b>$name</b> exists and
     * no default value to fall back was given.
     */
    private function getProperty(string $name, mixed $default = null): mixed
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }

        if ($default !== null) {
            return $default;
        }

        throw new OutOfBoundsException('Property "' . $name . '" does not exist.');
    }

    /**
     * Returns the value of a configured property as a boolean
     *
     * Throws an exception when no property with <b>$name</b> exists
     * and no default value to fall back was given.
     *
     * @param string $name The name of the property, e.g. "ignore-whitespace".
     * @param bool|null $default An optional default value to fall back instead of throwing an exception.
     * @return bool The value of a configured property as a boolean.
     * @throws OutOfBoundsException When no property for <b>$name</b> exists and
     * no default value to fall back was given.
     */
    public function getBooleanProperty(string $name, ?bool $default = null): bool
    {
        return in_array($this->getProperty($name, $default), ['true', 'on', 1], false);
    }

    /**
     * Returns the value of a configured property as an integer
     *
     * Throws an exception when no property with <b>$name</b> exists
     * and no default value to fall back was given.
     *
     * @param string $name The name of the property, e.g. "minimum".
     * @param int|null $default An optional default value to fall back instead of throwing an exception.
     * @return int The value of a configured property as an integer.
     * @throws OutOfBoundsException When no property for <b>$name</b> exists and
     * no default value to fall back was given.
     */
    public function getIntProperty(string $name, ?int $default = null): int
    {
        return (int) $this->getProperty($name, $default);
    }

    /**
     * Returns the raw string value of a configured property
     *
     * Throws an exception when no property with <b>$name</b> exists
     * and no default value to fall back was given.
     *
     * @param string $name The name of the property, e.g. "exceptions".
     * @param string|null $default An optional default value to fall back instead of throwing an exception.
     * @return string The raw string value of a configured property.
     * @throws OutOfBoundsException When no property for <b>$name</b> exists and
     * no default value to fall back was given.
     */
    public function getStringProperty(string $name, ?string $default = null): string
    {
        return (string) $this->getProperty($name, $default);
    }

    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }

    /**
     * This method adds a violation to all reports for this violation type and
     * for the given <b>$node</b> instance.
     *
     * @param AbstractNode<ASTNode> $node
     * @param array<int, string> $args
     * @param numeric $metric
     */
    protected function addViolation(
        AbstractNode $node,
        array $args = [],
        mixed $metric = null
    ): void {
        $message = [
            'message' => $this->message,
            'args' => $args,
        ];

        $ruleViolation = new RuleViolation($this, NodeInfoFactory::fromNode($node), $message, $metric);
        $this->report->addRuleViolation($ruleViolation);
    }

    /**
     * Apply the current rule on each method of a class node.
     *
     * @param ClassNode|EnumNode|InterfaceNode|TraitNode $node class node containing methods.
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function applyOnClassMethods(AbstractTypeNode $node): void
    {
        foreach ($node->getMethods() as $method) {
            if (!$this->strict && $method->hasSuppressWarningsAnnotationFor($this)) {
                continue;
            }

            $this->apply($method);
        }
    }
}
