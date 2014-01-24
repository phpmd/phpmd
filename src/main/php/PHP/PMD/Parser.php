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


use PDepend\Engine;
use PDepend\Report\CodeAwareGenerator;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;
use PDepend\Metrics\Analyzer;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTArtifactList;

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
       extends AbstractASTVisitor
    implements CodeAwareGenerator
{
    /**
     * The analysing rule-set instance.
     *
     * @var PHP_PMD_RuleSet[]
     */
    private $ruleSets = array();

    /**
     * The metric containing analyzer instances.
     *
     * @var \PDepend\Metrics\Analyzer[]
     */
    private $analyzers = array();

    /**
     * The raw PDepend code nodes.
     *
     * @var \PDepend\Source\AST\ASTArtifactList
     */
    private $artifacts = null;

    /**
     * The violation report used by this PDepend adapter.
     *
     * @var PHP_PMD_Report
     */
    private $report = null;

    /**
     * The wrapped PDepend Engine instance.
     *
     * @var PDepend\Engine
     */
    private $pdepend = null;

    /**
     * Constructs a new parser adapter instance.
     *
     * @param \PDepend\Engine $pdepend The context php depend instance.
     */
    public function __construct(Engine $pdepend)
    {
        $this->pdepend = $pdepend;
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

        $this->pdepend->addReportGenerator($this);
        $this->pdepend->analyze();

        foreach ($this->pdepend->getExceptions() as $exception) {
            $report->addError(new PHP_PMD_ProcessingError($exception->getMessage()));
        }
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
        $this->ruleSets[] = $ruleSet;
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
        $this->report = $report;
    }

    /**
     * Adds an analyzer to log. If this logger accepts the given analyzer it
     * with return <b>true</b>, otherwise the return value is <b>false</b>.
     *
     * @param \PDepend\Metrics\Analyzer $analyzer The analyzer to log.
     *
     * @return boolean
     */
    public function log(Analyzer $analyzer)
    {
        $this->analyzers[] = $analyzer;
    }

    /**
     * Closes the logger process and writes the output file.
     *
     * @return void
     * @throws PDepend\Report\NoLogOutputException If the no log target exists.
     */
    public function close()
    {
        // Set max nesting level, because we may get really deep data structures
        ini_set('xdebug.max_nesting_level', 8192);

        foreach ($this->artifacts as $node) {
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
        return array(
            'pdepend.analyzer.cyclomatic_complexity',
            'pdepend.analyzer.node_loc',
            'pdepend.analyzer.npath_complexity',
            'pdepend.analyzer.inheritance',
            'pdepend.analyzer.node_count',
            'pdepend.analyzer.hierarchy',
            'pdepend.analyzer.crap_index',
            'pdepend.analyzer.code_rank',
            'pdepend.analyzer.coupling',
            'pdepend.analyzer.class_level',
            'pdepend.analyzer.cohesion',
        );
    }

    /**
     * Visits a class node.
     *
     * @param \PDepend\Source\AST\ASTClass $node The current class node.
     *
     * @return void
     */
    public function visitClass(ASTClass $node)
    {
        if (!$node->isUserDefined()) {
            return;
        }

        $this->apply(new PHP_PMD_Node_Class($node));
        parent::visitClass($node);
    }

    /**
     * Visits a function node.
     *
     * @param ASTFunction $node The current function node.
     *
     * @return void
     */
    public function visitFunction(ASTFunction $node)
    {
        if ($node->getCompilationUnit()->getFileName() === null) {
            return;
        }

        $this->apply(new PHP_PMD_Node_Function($node));
    }

    /**
     * Visits an interface node.
     *
     * @param ASTInterface $node The current interface node.
     *
     * @return void
     */
    public function visitInterface(ASTInterface $node)
    {
        if (!$node->isUserDefined()) {
            return;
        }

        $this->apply(new PHP_PMD_Node_Interface($node));
        parent::visitInterface($node);
    }

    /**
     * Visits a method node.
     *
     * @param ASTMethod $node The method class node.
     *
     * @return void
     */
    public function visitMethod(ASTMethod $node)
    {
        if ($node->getCompilationUnit()->getFileName() === null) {
            return;
        }

        $this->apply(new PHP_PMD_Node_Method($node));
    }

    /**
     * Sets the context code nodes.
     *
     * @param \PDepend\Source\AST\ASTArtifactList $code The code nodes.
     *
     * @return void
     */
    public function setArtifacts(ASTArtifactList $artifacts)
    {
        $this->artifacts = $artifacts;
    }

    /**
     * Applies all rule-sets to the given <b>$node</b> instance.
     *
     * @param PHP_PMD_AbstractNode $node The context source node.
     *
     * @return void
     */
    private function apply(PHP_PMD_AbstractNode $node)
    {
        $this->collectMetrics($node);
        foreach ($this->ruleSets as $ruleSet) {
            $ruleSet->setReport($this->report);
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
    private function collectMetrics(PHP_PMD_AbstractNode $node)
    {
        $metrics = array();

        $pdepend = $node->getNode();
        foreach ($this->analyzers as $analyzer) {
            $metrics = array_merge($metrics, $analyzer->getNodeMetrics($pdepend));
        }
        $node->setMetrics($metrics);
    }
}
