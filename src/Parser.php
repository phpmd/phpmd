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
use LogicException;
use OutOfBoundsException;
use PDepend\Engine;
use PDepend\Metrics\Analyzer;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Report\CodeAwareGenerator;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException;
use PDepend\Source\AST\ASTCompilationUnitNotFoundException;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;
use PHPMD\Node\AbstractNode;
use PHPMD\Node\ClassNode;
use PHPMD\Node\EnumNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\InterfaceNode;
use PHPMD\Node\MethodNode;
use PHPMD\Node\TraitNode;

/**
 * Simple wrapper around the php depend engine.
 */
final class Parser extends AbstractASTVisitor implements CodeAwareGenerator
{
    /**
     * The analysing rule-set instance.
     *
     * @var list<RuleSet>
     */
    private array $ruleSets = [];

    /**
     * The metric containing analyzer instances.
     *
     * @var list<AnalyzerNodeAware>
     */
    private array $analyzers = [];

    /**
     * The raw PDepend code nodes.
     *
     * @var ASTArtifactList<ASTNamespace>
     */
    private ASTArtifactList $artifacts;

    /** The violation report used by this PDepend adapter. */
    private Report $report;

    /**
     * Constructs a new parser adapter instance.
     *
     * @param Engine $pdepend The wrapped PDepend Engine instance.
     */
    public function __construct(
        private readonly Engine $pdepend,
    ) {
    }

    /**
     * Parses the projects source and reports all detected errors and violations.
     *
     * @throws InvalidArgumentException
     */
    public function parse(Report $report): void
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
     */
    public function addRuleSet(RuleSet $ruleSet): void
    {
        $this->ruleSets[] = $ruleSet;
    }

    /**
     * Sets the violation report used by the rule-set.
     */
    public function setReport(Report $report): void
    {
        $this->report = $report;
    }

    /**
     * Adds an analyzer to log. If this logger accepts the given analyzer it
     * with return <b>true</b>, otherwise the return value is <b>false</b>.
     *
     * @param Analyzer $analyzer The analyzer to log.
     */
    public function log(Analyzer $analyzer): bool
    {
        if (!$analyzer instanceof AnalyzerNodeAware) {
            return false;
        }

        $this->analyzers[] = $analyzer;

        return true;
    }

    /**
     * Closes the logger process and writes the output file.
     */
    public function close(): void
    {
        // Set max nesting level, because we may get really deep data structures
        ini_set('xdebug.max_nesting_level', 8192);

        foreach ($this->artifacts as $node) {
            $this->dispatch($node);
        }
    }

    /**
     * Returns an <b>array</b> with accepted analyzer types. These types can be
     * concrete analyzer classes or one of the descriptive analyzer interfaces.
     *
     * @return string[]
     */
    public function getAcceptedAnalyzers(): array
    {
        return [
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
        ];
    }

    /**
     * Visits a class node.
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws LogicException
     */
    public function visitClass(ASTClass $node): void
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
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws LogicException
     */
    public function visitTrait(ASTTrait $node): void
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
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws LogicException
     */
    public function visitEnum(ASTEnum $node): void
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
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws LogicException
     */
    public function visitFunction(ASTFunction $node): void
    {
        if ($node->getCompilationUnit()?->getFileName() === null) {
            return;
        }

        $this->apply(new FunctionNode($node));
    }

    /**
     * Visits an interface node.
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws LogicException
     */
    public function visitInterface(ASTInterface $node): void
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
     * @throws ASTCompilationUnitNotFoundException
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws LogicException
     */
    public function visitMethod(ASTMethod $node): void
    {
        if ($node->getCompilationUnit()?->getFileName() === null) {
            return;
        }

        $this->apply(new MethodNode($node));
    }

    /**
     * Sets the context code nodes.
     *
     * @param ASTArtifactList<ASTNamespace> $artifacts
     */
    public function setArtifacts(ASTArtifactList $artifacts): void
    {
        $this->artifacts = $artifacts;
    }

    /**
     * Applies all rule-sets to the given <b>$node</b> instance.
     *
     * @param AbstractNode<ASTArtifact> $node
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws LogicException
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    private function apply(AbstractNode $node): void
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
     * @param AbstractNode<ASTArtifact> $node
     * @throws LogicException
     */
    private function collectMetrics(AbstractNode $node): void
    {
        $metrics = [];

        $pdepend = $node->getNode();
        foreach ($this->analyzers as $analyzer) {
            $metrics = [...$metrics, ...$analyzer->getNodeMetrics($pdepend)];
        }
        $node->setMetrics($metrics);
    }
}
