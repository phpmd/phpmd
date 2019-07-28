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

use PHPMD\Report;

/**
 * Base interface for a PHPMD rule.
 *
 * @since     1.1.0
 */
interface Rule
{
    /**
     * The default lowest rule priority.
     */
    const LOWEST_PRIORITY = 5;

    /**
     * The default highest rule priority.
     */
    const HIGHEST_PRIORITY = 1;

    /**
     * Returns the name for this rule instance.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the name for this rule instance.
     *
     * @param string $name
     * @return void
     */
    public function setName($name);

    /**
     * Returns the version since when this rule is available or <b>null</b>.
     *
     * @return string
     */
    public function getSince();

    /**
     * Sets the version since when this rule is available.
     *
     * @param string $since
     * @return void
     */
    public function setSince($since);

    /**
     * Returns the violation message text for this rule.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Sets the violation message text for this rule.
     *
     * @param string $message
     * @return void
     */
    public function setMessage($message);

    /**
     * Returns an url will external information for this rule.
     *
     * @return string
     */
    public function getExternalInfoUrl();

    /**
     * Sets an url will external information for this rule.
     *
     * @param string $externalInfoUrl
     * @return void
     */
    public function setExternalInfoUrl($externalInfoUrl);

    /**
     * Returns the description text for this rule instance.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the description text for this rule instance.
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description);

    /**
     * Returns a list of examples for this rule.
     *
     * @return array
     */
    public function getExamples();

    /**
     * Adds a code example for this rule.
     *
     * @param string $example
     * @return void
     */
    public function addExample($example);

    /**
     * Returns the priority of this rule.
     *
     * @return integer
     */
    public function getPriority();

    /**
     * Set the priority of this rule.
     *
     * @param integer $priority
     * @return void
     */
    public function setPriority($priority);

    /**
     * Returns the name of the parent rule-set instance.
     *
     * @return string
     */
    public function getRuleSetName();

    /**
     * Sets the name of the parent rule set instance.
     *
     * @param string $ruleSetName
     * @return void
     */
    public function setRuleSetName($ruleSetName);

    /**
     * Returns the violation report for this rule.
     *
     * @return Report
     */
    public function getReport();

    /**
     * Sets the violation report for this rule.
     *
     * @param Report $report
     * @return void
     */
    public function setReport(Report $report);

    /**
     * Adds a configuration property to this rule instance.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addProperty($name, $value);

    /**
     * Returns the value of a configured property as a boolean or throws an
     * exception when no property with <b>$name</b> exists.
     *
     * @param string $name
     * @return boolean
     * @throws \OutOfBoundsException When no property for <b>$name</b> exists.
     */
    public function getBooleanProperty($name);

    /**
     * Returns the value of a configured property as an integer or throws an
     * exception when no property with <b>$name</b> exists.
     *
     * @param string $name
     * @return integer
     * @throws \OutOfBoundsException When no property for <b>$name</b> exists.
     */
    public function getIntProperty($name);

    /**
     * This method should implement the violation analysis algorithm of concrete
     * rule implementations. All extending classes must implement this method.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node);
}
