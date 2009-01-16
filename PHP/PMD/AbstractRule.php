<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/php-pmd
 */

/**
 * This is the abstract base class for pmd rules.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/php-pmd
 */
abstract class PHP_PMD_AbstractRule
{
    /**
     * The defaukt lowest rule priority.
     */
    const LOWEST_PRIORITY = 5;

    /**
     * List of readable priority names.
     *
     * @var array(string) $_priorities
     */
    private static $_priorities = array(
        'High',
        'Medium High',
        'Medium',
        'Medium Low',
        'Low'
    );

    /**
     * The name for this rule instance.
     *
     * @var string $_name
     */
    private $_name = '';

    /**
     * The violation message text for this rule.
     *
     * @var string $_message
     */
    private $_message = '';

    /**
     * The version since when this rule is available.
     *
     * @var string $_since
     */
    private $_since = null;

    /**
     * An url will external information for this rule.
     *
     * @var string $_externalInfoUrl
     */
    private $_externalInfoUrl = '';

    /**
     * An optional description for this rule.
     *
     * @var string $_description
     */
    private $_description = '';

    /**
     * A list of code examples for this rule.
     *
     * @var array(string) $_examples
     */
    private $_examples = array();

    /**
     * The name of the parent rule-set instance.
     *
     * @var string $_ruleSetName
     */
    private $_ruleSetName = '';

    /**
     * The priority of this rule.
     *
     * @var integer $_priority
     */
    private $_priority = self::LOWEST_PRIORITY;

    /**
     * Configuration properties for this rule instance.
     *
     * @var array(string=>string) $_properties
     */
    private $_properties = array();

    /**
     * Returns the name for this rule instance.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets the name for this rule instance.
     *
     * @param string $name The rule name.
     *
     * @return void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Returns the version since when this rule is available or <b>null</b>.
     *
     * @return string
     */
    public function getSince()
    {
        return $this->_since;
    }

    /**
     * Sets the version since when this rule is available.
     *
     * @param string $since The version number.
     *
     * @return void
     */
    public function setSince($since)
    {
        $this->_since = $since;
    }

    /**
     * Returns the violation message text for this rule.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Sets the violation message text for this rule.
     *
     * @param string $message The violation message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->_message = $message;
    }

    /**
     * Returns an url will external information for this rule.
     *
     * @return string
     */
    public function getExternalInfoUrl()
    {
        return $this->_externalInfoUrl;
    }

    /**
     * Sets an url will external information for this rule.
     *
     * @param string $externalInfoUrl The info url.
     *
     * @return void
     */
    public function setExternalInfoUrl($externalInfoUrl)
    {
        $this->_externalInfoUrl = $externalInfoUrl;
    }

    /**
     * Returns the description text for this rule instance.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Sets the description text for this rule instance.
     *
     * @param string $description The description text.
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * Returns a list of examples for this rule.
     *
     * @return array(string)
     */
    public function getExamples()
    {
        return $this->_examples;
    }

    /**
     * Adds a code example for this rule.
     *
     * @param string $example The code example.
     *
     * @return void
     */
    public function addExample($example)
    {
        $this->_examples[] = $example;
    }

    /**
     * Returns the priority of this rule.
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Set the priority of this rule.
     *
     * @param integer $priority The rule priority
     *
     * @return void
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;
    }

    /**
     * Returns the name of the parent rule-set instance.
     *
     * @return string
     */
    public function getRuleSetName()
    {
        return $this->_ruleSetName;
    }

    /**
     * Sets the name of the parent rule set instance.
     *
     * @param string $ruleSetName The rule-set name.
     *
     * @return void
     */
    public function setRuleSetName($ruleSetName)
    {
        $this->_ruleSetName = $ruleSetName;
    }

    /**
     * Adds a configuration property to this rule instance.
     *
     * @param string $name  The property name.
     * @param string $value The property value.
     *
     * @return void
     */
    public function addProperty($name, $value)
    {
        $this->_properties[$name] = $value;
    }

    public function getIntProperty($name)
    {
        if (isset($this->_properties[$name])) {
            return (int) $this->_properties[$name];
        }
        return 0;
    }

    protected function addViolation(PHP_Depend_Code_NodeI $node, array $args)
    {
        $search  = array();
        $replace = array();
        foreach ($args as $index => $value) {
            $search[]  = '{' . $index . '}';
            $replace[] = $value;
        }

        echo str_replace($search, $replace, $this->_message), PHP_EOL;
    }

    public abstract function apply(PHP_Depend_Code_NodeI $node, array $metrics);
}
?>
