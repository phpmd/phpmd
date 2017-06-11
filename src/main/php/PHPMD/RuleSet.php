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

/**
 * This class is a collection of concrete source analysis rules.
 */
class RuleSet implements \IteratorAggregate
{
    /**
     * Should this rule set force the strict mode.
     *
     * @var boolean
     * @since 1.2.0
     */
    private $strict = false;

    /**
     * The name of the file where this set is specified.
     *
     * @var string
     */
    private $fileName = '';

    /**
     * The name of this rule-set.
     *
     * @var string
     */
    private $name = '';

    /**
     * An optional description for this rule-set.
     *
     * @var string
     */
    private $description = '';

    /**
     * The violation report used by the rule-set.
     *
     * @var \PHPMD\Report
     */
    private $report;

    /**
     * Mapping between marker interfaces and concrete context code node classes.
     *
     * @var array(string=>string)
     */
    private $applyTo = array(
        'PHPMD\\Rule\\ClassAware'     => 'PHPMD\\Node\\ClassNode',
        'PHPMD\\Rule\\FunctionAware'  => 'PHPMD\\Node\\FunctionNode',
        'PHPMD\\Rule\\InterfaceAware' => 'PHPMD\\Node\\InterfaceNode',
        'PHPMD\\Rule\\MethodAware'    => 'PHPMD\\Node\\MethodNode',
    );

    /**
     * Mapping of rules that apply to a concrete code node type.
     *
     * @var array(string=>array)
     */
    private $rules = array(
        'PHPMD\\Node\\ClassNode'     =>  array(),
        'PHPMD\\Node\\FunctionNode'  =>  array(),
        'PHPMD\\Node\\InterfaceNode' =>  array(),
        'PHPMD\\Node\\MethodNode'    =>  array(),
    );

    /**
     * Returns the file name where the definition of this rule-set comes from.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Sets the file name where the definition of this rule-set comes from.
     *
     * @param string $fileName The file name.
     * @return void
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Returns the name of this rule-set.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of this rule-set.
     *
     * @param string $name The name of this rule-set.
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the description text for this rule-set instance.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description text for this rule-set instance.
     *
     * @param string $description The description text.
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Activates the strict mode for this rule set instance.
     *
     * @return void
     * @since 1.2.0
     */
    public function setStrict()
    {
        $this->strict = true;
    }

    /**
     * Returns the violation report used by the rule-set.
     *
     * @return \PHPMD\Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Sets the violation report used by the rule-set.
     *
     * @param \PHPMD\Report $report
     * @return void
     */
    public function setReport(Report $report)
    {
        $this->report = $report;
    }

    /**
     * This method returns a rule by its name or <b>null</b> if it doesn't exist.
     *
     * @param string $name
     * @return \PHPMD\Rule
     */
    public function getRuleByName($name)
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
     *
     * @return \Iterator
     */
    public function getRules()
    {
        $result = array();
        foreach ($this->rules as $rules) {
            foreach ($rules as $rule) {
                if (in_array($rule, $result, true) === false) {
                    $result[] = $rule;
                }
            }
        }

        return new \ArrayIterator($result);
    }

    /**
     * Adds a new rule to this rule-set.
     *
     * @param \PHPMD\Rule $rule
     * @return void
     */
    public function addRule(Rule $rule)
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
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
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
            $rule->apply($node);
        }
    }

    /**
     * Returns an iterator with all rules that are part of this rule-set.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->getRules();
    }
}
