<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://phpmd.org
 */

require_once 'PHP/PMD/Node/Class.php';
require_once 'PHP/PMD/Node/Function.php';
require_once 'PHP/PMD/Node/Interface.php';
require_once 'PHP/PMD/Node/Method.php';

/**
 * Simple wrapper around the php depend engine.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://phpmd.org
 */
class PHP_PMD_Parser
       extends PHP_Depend_Visitor_AbstractVisitor
    implements PHP_Depend_Log_LoggerI,
               PHP_Depend_Log_CodeAwareI
{
    /**
     * The analysing rule-set instance.
     *
     * @var array(PHP_PMD_RuleSet) $_ruleSets
     */
    private $_ruleSets = array();

    /**
     * The metric containing analyzer instances.
     *
     * @var array(PHP_Depend_Metrics_AnalyzerI) $_analyzers
     */
    private $_analyzers = array();

    /**
     * The raw PHP_Depend code nodes.
     *
     * @var PHP_Depend_Code_NodeIterator
     */
    private $_code = null;

    /**
     * The violation report used by this PHP_Depend adapter.
     *
     * @var PHP_PMD_Report $_report
     */
    private $_report = null;

    /**
     * The wrapped PHP_Depend instance.
     *
     * @var PHP_Depend
     */
    private $_pdepend = null;

    /**
     * Constructs a new parser adapter instance.
     *
     * @param PHP_Depend $pdepend The context php depend instance.
     */
    public function __construct(PHP_Depend $pdepend)
    {
        $this->_pdepend = $pdepend;
    }

    /**
     * Parses the projects source and reports all detected errors and violations.
     *
     * @param PHP_PMD_Report $report The phpmd error and violation report.
     *
     * @return void
     */
    public function parse(PHP_PMD_Report $report)
    {
        $this->setReport($report);

        $this->_pdepend->addLogger($this);
        $this->_pdepend->analyze();
    }

    /**
     * Adds a new analysis rule-set to this adapter.
     *
     * @param PHP_PMD_RuleSet $ruleSet The new rule-set instance.
     *
     * @return void
     */
    public function addRuleSet(PHP_PMD_RuleSet $ruleSet)
    {
        $this->_ruleSets[] = $ruleSet;
    }

    /**
     * Sets the violation report used by the rule-set.
     *
     * @param PHP_PMD_Report $report The violation report to use.
     *
     * @return void
     */
    public function setReport(PHP_PMD_Report $report)
    {
        $this->_report = $report;
    }

    /**
     * Adds an analyzer to log. If this logger accepts the given analyzer it
     * with return <b>true</b>, otherwise the return value is <b>false</b>.
     *
     * @param PHP_Depend_Metrics_AnalyzerI $analyzer The analyzer to log.
     *
     * @return boolean
     */
    public function log(PHP_Depend_Metrics_AnalyzerI $analyzer)
    {
        $this->_analyzers[] = $analyzer;
    }

    /**
     * Closes the logger process and writes the output file.
     *
     * @return void
     * @throws PHP_Depend_Log_NoLogOutputException If the no log target exists.
     */
    public function close()
    {
        foreach ($this->_code as $node) {
            $node->accept($this);
        }
    }

    /**
     * Returns an <b>array</b> with accepted analyzer types. These types can be
     * concrete analyzer classes or one of the descriptive analyzer interfaces.
     *
     * @return array(string)
     */
    public function getAcceptedAnalyzers()
    {
        return array('PHP_Depend_Metrics_NodeAwareI');
    }

    /**
     * Visits a class node.
     *
     * @param PHP_Depend_Code_Class $node The current class node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitClass()
     */
    public function visitClass(PHP_Depend_Code_Class $node)
    {
        if (!$node->isUserDefined()) {
            return;
        }

        $this->_apply(new PHP_PMD_Node_Class($node));
        parent::visitClass($node);
    }

    /**
     * Visits a function node.
     *
     * @param PHP_Depend_Code_Function $node The current function node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitFunction()
     */
    public function visitFunction(PHP_Depend_Code_Function $node)
    {
        if ($node->getSourceFile()->getFileName() === null) {
            return;
        }

        $this->_apply(new PHP_PMD_Node_Function($node));
    }

    /**
     * Visits an interface node.
     *
     * @param PHP_Depend_Code_Interface $node The current interface node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitInterface()
     */
    public function visitInterface(PHP_Depend_Code_Interface $node)
    {
        if (!$node->isUserDefined()) {
            return;
        }
        
        $this->_apply(new PHP_PMD_Node_Interface($node));
        parent::visitInterface($node);
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Depend_Code_Method $node The method class node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitMethod()
     */
    public function visitMethod(PHP_Depend_Code_Method $node)
    {
        if ($node->getSourceFile()->getFileName() === null) {
            return;
        }

        $this->_apply(new PHP_PMD_Node_Method($node));
    }

    /**
     * Sets the context code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $code The code nodes.
     *
     * @return void
     */
    public function setCode(PHP_Depend_Code_NodeIterator $code)
    {
        $this->_code = $code;
    }

    /**
     * Applies all rule-sets to the given <b>$node</b> instance.
     *
     * @param PHP_PMD_AbstractNode $node The context source node.
     *
     * @return void
     */
    private function _apply(PHP_PMD_AbstractNode $node)
    {
        $this->_collectMetrics($node);
        foreach ($this->_ruleSets as $ruleSet) {
            $ruleSet->setReport($this->_report);
            $ruleSet->apply($node);
        }
    }

    /**
     * Collects the collected metrics for the given node and adds them to the
     * <b>$node</b>.
     *
     * @param PHP_PMD_AbstractNode $node The context source node.
     *
     * @return void
     */
    private function _collectMetrics(PHP_PMD_AbstractNode $node)
    {
        $metrics = array();

        $pdepend = $node->getNode();
        foreach ($this->_analyzers as $analyzer) {
            $metrics = array_merge($metrics, $analyzer->getNodeMetrics($pdepend));
        }
        $node->setMetrics($metrics);
    }
}
