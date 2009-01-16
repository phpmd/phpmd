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
 * @category   PHP
 * @package    PHP_PMD
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/pmd
 */

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/RuleSet.php';
require_once 'PHP/PMD/RuleSetNotFoundException.php';

/**
 * This factory class is used to create the {@link PHP_PMD_RuleSet} instance
 * that PHP_PMD will use to analyze the source code.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/pmd
 */
class PHP_PMD_RuleSetFactory
{
    private $_location = '@data_dir@';

    /**
     * The minimum priority for rules to load.
     *
     * @var integer $_minPriority
     */
    private $_minimumPriority = PHP_PMD_AbstractRule::LOWEST_PRIORITY;

    public function __construct()
    {
        if (strpos($this->_location, '@data_dir') === 0) {
            $this->_location = dirname(__FILE__) . '/../../';
        }
    }

    /**
     * Sets the minimum priority that a rule must have.
     *
     * @param integer $minimumPriority The minimum priority value.
     *
     * @return void
     */
    public function setMinimumPriority($minimumPriority)
    {
        $this->_minimumPriority = $minimumPriority;
    }

    public function createRuleSets($ruleSetFileNames)
    {
        $ruleSets = array();

        $ruleSetFileName = strtok($ruleSetFileNames, ',');
        while ($ruleSetFileName !== false) {
            $ruleSets[] = $this->createSingleRuleSet($ruleSetFileName);

            $ruleSetFileName = strtok(',');
        }
        return $ruleSets;
    }

    public function createSingleRuleSet($ruleSetOrFileName)
    {
        $fileName = $this->_createRuleSetFileName($ruleSetOrFileName);
        return $this->_parseRuleSetNode($fileName);
    }

    private function _createRuleSetFileName($ruleSetOrFileName)
    {
        if (file_exists($ruleSetOrFileName) === true) {
            return $ruleSetOrFileName;
        }

        $fileName = $this->_location . '/' . $ruleSetOrFileName;
        if (file_exists($fileName) === true) {
            return $fileName;
        }

        $fileName = $this->_location . '/rulesets/' . $ruleSetOrFileName . '.xml';
        if (file_exists($fileName) === true) {
            return $fileName;
        }

        $fileName = getcwd() . '/' . $ruleSetOrFileName;
        if (file_exists($fileName) === true) {
            return $fileName;
        }

        $fileName = getcwd() . '/rulesets/' . $ruleSetOrFileName . '.xml';
        if (file_exists($fileName) === true) {
            return $fileName;
        }

        throw new PHP_PMD_RuleSetNotFoundException($ruleSetOrFileName);
    }

    private function _parseRuleSetNode($fileName)
    {
        // Hide error messages
        $libxml = libxml_use_internal_errors(true);

        if (($xml = simplexml_load_file($fileName)) === false) {
            // Reset error handling to previous setting
            libxml_use_internal_errors($libxml);


            throw new RuntimeException(trim(libxml_get_last_error()->message));
        }

        $ruleSet = new PHP_PMD_RuleSet();
        $ruleSet->setFileName($fileName);
        $ruleSet->setName((string) $xml['name']);

        foreach ($xml->children() as $node) {
            if ($node->getName() === 'description') {
                $ruleSet->setDescription((string) $node);
            } else if ($node->getName() === 'rule') {
                $this->_parseRuleNode($ruleSet, $node);
            }
        }

        return $ruleSet;
    }

    private function _parseRuleNode(PHP_PMD_RuleSet $ruleSet,
                                    SimpleXMLElement $node)
    {
        if (substr($node['ref'], -3, 3) === 'xml') {
            $this->_parseRuleSetReferenceNode($ruleSet, $node);
        } else if ('' === (string) $node['ref']) {
            $this->_parseSingleRuleNode($ruleSet, $node);
        } else {
            $this->_parseRuleReferenceNode($ruleSet, $node);
        }
    }

    private function _parseRuleSetReferenceNode(PHP_PMD_RuleSet $ruleSet,
                                                SimpleXMLElement $ruleSetNode)
    {
        $ruleSetFactory = new PHP_PMD_RuleSetFactory();
        $ruleSetFactory->setMinimumPriority($this->_minimumPriority);

        $rules = $ruleSetFactory->createRuleSets((string) $ruleSetNode['ref']);
        foreach ($rules as $rule) {
            $ruleSet->addRule($rule);
        }
    }

    private function _parseSingleRuleNode(PHP_PMD_RuleSet $ruleSet,
                                          SimpleXMLElement $ruleNode)
    {
        $className = (string) $ruleNode['class'];
        $fileName  = strtr($className, '_', '/') . '.php';

        if (($fp = fopen($fileName, 'r', true)) === false) {
            throw new RuntimeException('Cannot find class file for: ' . $className);
        }

        include_once $fileName;

        if (class_exists($className) === false) {
            throw new RuntimeException('Cannot find class: ' . $className);
        }

        /* @var $rule PHP_PMD_AbstractRule */
        $rule = new $className();
        $rule->setName((string) $ruleNode['name']);
        $rule->setMessage((string) $ruleNode['message']);
        $rule->setExternalInfoUrl((string) $ruleNode['externalInfoUrl']);

        $rule->setRuleSetName($ruleSet->getName());

        if (trim($ruleNode['since']) !== '') {
            $rule->setSince((string) $ruleNode['since']);
        }

        foreach ($ruleNode->children() as $node) {
            if ($node->getName() === 'description') {
                $rule->setDescription((string) $node);
            } else if ($node->getName() === 'example') {
                $rule->addExample((string) $node);
            } else if ($node->getName() === 'priority') {
                $rule->setPriority((integer) $node);
            } else if ($node->getName() === 'properties') {
                $this->_parsePropertiesNode($rule, $node);
            }
        }

        if ($rule->getPriority() <= $this->_minimumPriority) {
            $ruleSet->addRule($rule);
        }
    }

    private function _parseRuleReferenceNode(PHP_PMD_RuleSet $ruleSet,
                                             SimpleXMLElement $ruleNode)
    {
        $ref = (string) $ruleNode['ref'];

        $fileName = substr($ref, 0, strpos($ref, '.xml/') + 4);
        $fileName = $this->_createRuleSetFileName($fileName);

        $ruleName = substr($ref, strpos($ref, '.xml/') + 5);

        $ruleSetFactory = new PHP_PMD_RuleSetFactory();

        $ruleSet = $ruleSetFactory->createSingleRuleSet($fileName);
        $rule    = $ruleSet->getRuleByName($ruleName);

        if (trim($ruleNode['name']) !== '') {
            $rule->setName((string) $ruleNode['name']);
        }
        if (trim($ruleNode['message']) !== '') {
            $rule->setMessage((string) $ruleNode['message']);
        }
        if (trim($ruleNode['externalInfoUrl']) !== '') {
            $rule->setExternalInfoUrl((string) $ruleNode['externalInfoUrl']);
        }

        foreach ($ruleNode->children() as $node) {
            if ($node->getName() === 'description') {
                $rule->setDescription((string) $node);
            } else if ($node->getName() === 'example') {
                $rule->addExample((string) $node);
            } else if ($node->getName() === 'priority') {
                $rule->setPriority((integer) $node);
            } else if ($node->getName() === 'properties') {
                $this->_parsePropertiesNode($rule, $node);
            }
        }

        if ($rule->getPriority() <= $this->_minimumPriority) {
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
     *
     * @param PHP_PMD_AbstractRule $rule           The context rule object.
     * @param SimpleXMLElement     $propertiesNode The raw properties xml node.
     *
     * @return void
     */
    private function _parsePropertiesNode(PHP_PMD_AbstractRule $rule,
                                          SimpleXMLElement $propertiesNode)
    {
        foreach ($propertiesNode->children() as $node) {
            if ($node->getName() !== 'property') {
                continue;
            }

            $name  = trim($node['name']);
            $value = trim($node['value']);
            if ($name !== '' && $value !== '') {
                $rule->addProperty($name, $value);
            }
        }
    }
}
?>
