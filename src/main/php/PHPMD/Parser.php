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

use PDepend\Engine;
use PDepend\Report\CodeAwareGenerator;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;
use PDepend\Metrics\Analyzer;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTArtifactList;
use PHPMD\Node\ClassNode;
use PHPMD\Node\EnumNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\InterfaceNode;
use PHPMD\Node\MethodNode;
use PHPMD\Node\TraitNode;

/**
 * Simple wrapper around the php depend engine.
 */
class Parser extends AbstractASTVisitor implements CodeAwareGenerator
{
    /**
     * The analysing rule-set instance.
     *
     * @var \PHPMD\RuleSet[]
     */
    private $ruleSets = array();

    /**
     * The metric containing analyzer instances.
     *
     * @var \PDepend\Metrics\AnalyzerNodeAware[]
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
     * @var \PHPMD\Report
     */
    private $report = null;

    /**
     * The wrapped PDepend Engine instance.
     *
     * @var \PDepend\Engine
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
     * @param \PHPMD\Report $report
     * @return void
     */
    public function parse(Report $report)
    {
        $this->setReport($report);

        $this->pdepend->addReportGenerator($this);
        $this->pdepend->analyze();

        foreach ($this->pdepend->getExceptions() as $exception) {
            $report->addError(new ProcessingError($exception->getMessage()));
        }
    }

    /**
     * Adds a new analysis rule-set to this adapter.
     *
     * @param \PHPMD\RuleSet $ruleSet
     * @return void
     */
    public function addRuleSet(RuleSet $ruleSet)
    {
        $this->ruleSets[] = $ruleSet;
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
     * Adds an analyzer to log. If this logger accepts the given analyzer it
     * with return <b>true</b>, otherwise the return value is <b>false</b>.
     *
     * @param \PDepend\Metrics\Analyzer $analyzer The analyzer to log.
     * @return void
     */
    public function log(Analyzer $analyzer)
    {
        $this->analyzers[] = $analyzer;
    }

    /**
     * Closes the logger process and writes the output file.
     *
     * @return void
     * @throws \PDepend\Report\NoLogOutputException If the no log target exists.
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
     * @return string[]
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
     * @param \PDepend\Source\AST\ASTClass $node
     * @return void
     */
    public function visitClass(ASTClass $node)
    {
        if (!$node->isUserDefined()) {
            return;
        }

        $this->apply(new ClassNode($node));
        parent::visitClass($node);
    }

    /**
     * Visits a trait node.
     *
     * @param \PDepend\Source\AST\ASTTrait $node
     * @return void
     */
    public function visitTrait(ASTTrait $node)
    {
        if (!$node->isUserDefined()) {
            return;
        }

        $this->apply(new TraitNode($node));
        parent::visitTrait($node);
    }

    /**
     * Visits a enum node.
     *
     * @param \PDepend\Source\AST\ASTEnum $node
     * @return void
     */
    public function visitEnum(ASTEnum $node)
    {
        if (!$node->isUserDefined()) {
            return;
        }

        $this->apply(new EnumNode($node));
        parent::visitEnum($node);
    }

    /**
     * Visits a function node.
     *
     * @param \PDepend\Source\AST\ASTFunction $node
     * @return void
     */
    public function visitFunction(ASTFunction $node)
    {
        if ($node->getCompilationUnit()->getFileName() === null) {
            return;
        }

        $this->apply(new FunctionNode($node));
    }

    /**
     * Visits an interface node.
     *
     * @param \PDepend\Source\AST\ASTInterface $node
     * @return void
     */
    public function visitInterface(ASTInterface $node)
    {
        if (!$node->isUserDefined()) {
            return;
        }

        $this->apply(new InterfaceNode($node));
        parent::visitInterface($node);
    }

    /**
     * Visits a method node.
     *
     * @param \PDepend\Source\AST\ASTMethod $node
     * @return void
     */
    public function visitMethod(ASTMethod $node)
    {
        if ($node->getCompilationUnit()->getFileName() === null) {
            return;
        }

        $this->apply(new MethodNode($node));
    }

    /**
     * Sets the context code nodes.
     *
     * @param \PDepend\Source\AST\ASTArtifactList $artifacts
     * @return void
     */
    public function setArtifacts(ASTArtifactList $artifacts)
    {
        $this->artifacts = $artifacts;
    }

    /**
     * Applies all rule-sets to the given <b>$node</b> instance.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    private function apply(AbstractNode $node)
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
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    private function collectMetrics(AbstractNode $node)
    {
        $metrics = array();

        $pdepend = $node->getNode();
        foreach ($this->analyzers as $analyzer) {
            $metrics = array_merge($metrics, $analyzer->getNodeMetrics($pdepend));
        }
        $node->setMetrics($metrics);
    }
}
