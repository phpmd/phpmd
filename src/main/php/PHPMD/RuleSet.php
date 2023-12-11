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
use IteratorAggregate;

/**
 * This class is a collection of concrete source analysis rules.
 */
class RuleSet implements IteratorAggregate
{
    /**
     * Should this rule set force the strict mode.
     *
     * @since 1.2.0
     */
    private bool $strict = false;

    /**
     * The name of the file where this set is specified.
     */
    private string $fileName = '';

    /**
     * The name of this rule-set.
     */
    private string $name = '';

    /**
     * An optional description for this rule-set.
     */
    private string $description = '';

    /**
     * The violation report used by the rule-set.
     */
    private ?Report $report = null;

    /**
     * Mapping between marker interfaces and concrete context code node classes.
     *
     * @var array(string=>string)
     */
    private array $applyTo = [
        'PHPMD\\Rule\\ClassAware' => 'PHPMD\\Node\\ClassNode',
        'PHPMD\\Rule\\TraitAware' => 'PHPMD\\Node\\TraitNode',
        'PHPMD\\Rule\\EnumAware' => 'PHPMD\\Node\\EnumNode',
        'PHPMD\\Rule\\FunctionAware' => 'PHPMD\\Node\\FunctionNode',
        'PHPMD\\Rule\\InterfaceAware' => 'PHPMD\\Node\\InterfaceNode',
        'PHPMD\\Rule\\MethodAware' => 'PHPMD\\Node\\MethodNode',
    ];

    /**
     * Mapping of rules that apply to a concrete code node type.
     *
     * @var array(string=>array)
     */
    private $rules = [
        'PHPMD\\Node\\ClassNode' => [],
        'PHPMD\\Node\\TraitNode' => [],
        'PHPMD\\Node\\EnumNode' => [],
        'PHPMD\\Node\\FunctionNode' => [],
        'PHPMD\\Node\\InterfaceNode' => [],
        'PHPMD\\Node\\MethodNode' => [],
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
     * @return void
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
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the description text for this rule-set instance.
     *
     * @return string
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
     * This method returns a rule by its name or <b>null</b> if it doesn't exist.
     */
    public function getRuleByName(string $name): ?Rule
    {
        foreach ($this->getRules() as $rule) {
            if ($rule->getName() === $name) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * This method returns an iterator will all rules that belong to this
     * rule-set.
     */
    public function getRules(): ArrayIterator
    {
        $result = [];
        foreach ($this->rules as $rules) {
            foreach ($rules as $rule) {
                if (in_array($rule, $result, true) === false) {
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
     */
    public function apply(AbstractNode $node): void
    {
        // Current node type
        $className = get_class($node);

        // Check for valid node type
        if (!isset($this->rules[$className])) {
            return;
        }

        // Apply all rules to this node
        foreach ($this->rules[$className] as $rule) {
            /** @var $rule Rule */
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
     */
    public function getIterator(): ArrayIterator
    {
        return $this->getRules();
    }
}
