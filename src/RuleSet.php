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

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use OutOfBoundsException;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException;
use PHPMD\Node\AbstractNode;
use PHPMD\Node\ClassNode;
use PHPMD\Node\EnumNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\InterfaceNode;
use PHPMD\Node\MethodNode;
use PHPMD\Node\TraitNode;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\EnumAware;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\InterfaceAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Rule\TraitAware;

/**
 * This class is a collection of concrete source analysis rules.
 *
 * @implements IteratorAggregate<int, Rule>
 */
class RuleSet implements IteratorAggregate
{
    /**
     * Should this rule set force the strict mode.
     *
     * @since 1.2.0
     */
    private bool $strict = false;

    /** The name of the file where this set is specified. */
    private string $fileName = '';

    /** The name of this rule-set. */
    private string $name = '';

    /** An optional description for this rule-set. */
    private string $description = '';

    /** The violation report used by the rule-set. */
    private Report $report;

    /**
     * Mapping between marker interfaces and concrete context code node classes.
     *
     * @var array<class-string, class-string<AbstractNode<ASTArtifact>>>
     */
    private array $applyTo = [
        ClassAware::class => ClassNode::class,
        TraitAware::class => TraitNode::class,
        EnumAware::class => EnumNode::class,
        FunctionAware::class => FunctionNode::class,
        InterfaceAware::class => InterfaceNode::class,
        MethodAware::class => MethodNode::class,
    ];

    /**
     * Mapping of rules that apply to a concrete code node type.
     *
     * @var array<class-string<AbstractNode<ASTArtifact>>, list<Rule>>
     */
    private array $rules = [
        ClassNode::class => [],
        TraitNode::class => [],
        EnumNode::class => [],
        FunctionNode::class => [],
        InterfaceNode::class => [],
        MethodNode::class => [],
    ];

    /**
     * Returns the file name where the definition of this rule-set comes from.
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Sets the file name where the definition of this rule-set comes from.
     *
     * @param string $fileName The file name.
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * Returns the name of this rule-set.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of this rule-set.
     *
     * @param string $name The name of this rule-set.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the description text for this rule-set instance.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description text for this rule-set instance.
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Activates the strict mode for this rule set instance.
     *
     * @since 1.2.0
     */
    public function setStrict(): void
    {
        $this->strict = true;
    }

    /**
     * Check if ruleset has been setStrict().
     *
     * @since 3.0.0
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Returns the violation report used by the rule-set.
     */
    public function getReport(): Report
    {
        return $this->report;
    }

    /**
     * Sets the violation report used by the rule-set.
     */
    public function setReport(Report $report): void
    {
        $this->report = $report;
    }

    /**
     * This method returns a rule by its name or throws an exception
     *
     * @param string $name The name of the rule to get.
     * @throws RuleByNameNotFoundException When the rule could not be found.
     */
    public function getRuleByName(string $name): Rule
    {
        foreach ($this->getRules() as $rule) {
            if ($rule->getName() === $name) {
                return $rule;
            }
        }

        throw new RuleByNameNotFoundException($name);
    }

    /**
     * This method returns an iterator will all rules that belong to this
     * rule-set.
     *
     * @return ArrayIterator<int, Rule>
     */
    public function getRules(): ArrayIterator
    {
        $result = [];
        foreach ($this->rules as $rules) {
            foreach ($rules as $rule) {
                if (!in_array($rule, $result, true)) {
                    $result[] = $rule;
                }
            }
        }

        return new ArrayIterator($result);
    }

    /**
     * Adds a new rule to this rule-set.
     */
    public function addRule(Rule $rule): void
    {
        foreach ($this->applyTo as $applyTo => $type) {
            if ($rule instanceof $applyTo) {
                $this->rules[$type][] = $rule;
            }
        }
    }

    /**
     * Applies all registered rules that match against the concrete node type.
     *
     * @param AbstractNode<ASTArtifact> $node
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    public function apply(AbstractNode $node): void
    {
        // Current node type
        $className = $node::class;

        // Check for valid node type
        if (!isset($this->rules[$className])) {
            return;
        }

        // Apply all rules to this node
        foreach ($this->rules[$className] as $rule) {
            if ($node->hasSuppressWarningsAnnotationFor($rule) && !$this->strict) {
                continue;
            }
            $rule->setReport($this->report);
            if (method_exists($rule, 'setStrict')) {
                $rule->setStrict($this->strict);
            }
            $rule->apply($node);
        }
    }

    /**
     * Returns an iterator with all rules that are part of this rule-set.
     *
     * @return ArrayIterator<int, Rule>
     */
    public function getIterator(): ArrayIterator
    {
        return $this->getRules();
    }
}
