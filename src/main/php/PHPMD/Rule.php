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

/**
 * Base interface for a PHPMD rule.
 *
 * @since 1.1.0
 */
interface Rule
{
    /** The default lowest rule priority. */
    final public const LOWEST_PRIORITY = 5;

    /** The default highest rule priority. */
    final public const HIGHEST_PRIORITY = 1;

    /**
     * Returns the name for this rule instance.
     */
    public function getName(): string;

    /**
     * Sets the name for this rule instance.
     */
    public function setName(string $name): void;

    /**
     * Returns the version since when this rule is available or <b>null</b>.
     */
    public function getSince(): ?string;

    /**
     * Sets the version since when this rule is available.
     */
    public function setSince(?string $since): void;

    /**
     * Returns the violation message text for this rule.
     */
    public function getMessage(): string;

    /**
     * Sets the violation message text for this rule.
     */
    public function setMessage(string $message): void;

    /**
     * Returns an url will external information for this rule.
     */
    public function getExternalInfoUrl(): string;

    /**
     * Sets an url will external information for this rule.
     */
    public function setExternalInfoUrl(string $externalInfoUrl): void;

    /**
     * Returns the description text for this rule instance.
     */
    public function getDescription(): string;

    /**
     * Sets the description text for this rule instance.
     */
    public function setDescription(string $description): void;

    /**
     * Returns a list of examples for this rule.
     *
     * @return list<string>
     */
    public function getExamples(): array;

    /**
     * Adds a code example for this rule.
     */
    public function addExample(string $example): void;

    /**
     * Returns the priority of this rule.
     */
    public function getPriority(): int;

    /**
     * Set the priority of this rule.
     */
    public function setPriority(int $priority): void;

    /**
     * Returns the name of the parent rule-set instance.
     */
    public function getRuleSetName(): string;

    /**
     * Sets the name of the parent rule set instance.
     */
    public function setRuleSetName(string $ruleSetName): void;

    /**
     * Returns the violation report for this rule.
     */
    public function getReport(): Report;

    /**
     * Sets the violation report for this rule.
     */
    public function setReport(Report $report): void;

    /**
     * Adds a configuration property to this rule instance.
     */
    public function addProperty(string $name, string $value): void;

    /**
     * Returns the value of a configured property as a boolean or throws an
     * exception when no property with <b>$name</b> exists.
     *
     * @throws OutOfBoundsException When no property for <b>$name</b> exists.
     */
    public function getBooleanProperty(string $name): bool;

    /**
     * Returns the value of a configured property as an integer or throws an
     * exception when no property with <b>$name</b> exists.
     *
     * @throws OutOfBoundsException When no property for <b>$name</b> exists.
     */
    public function getIntProperty(string $name): int;

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
     * no non-null default value to fall back was given.
     */
    public function getStringProperty(string $name, ?string $default = null): string;

    /**
     * This method should implement the violation analysis algorithm of concrete
     * rule implementations. All extending classes must implement this method.
     *
     * @param AbstractNode<ASTNode> $node The node to check upon.
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    public function apply(AbstractNode $node): void;
}
