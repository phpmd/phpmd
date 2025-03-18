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

use ErrorException;
use Iterator;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use PHPMD\Node\ClassNode;
use PHPMD\Node\EnumNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\InterfaceNode;
use PHPMD\Node\MethodNode;
use PHPMD\Node\NodeInfo;
use PHPMD\Node\TraitNode;
use PHPMD\Rule\Design\TooManyFields;
use PHPMD\Stubs\RuleStub;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;

/**
 * Abstract base class for PHPMD test cases.
 */
abstract class AbstractTestCase extends AbstractStaticTestCase
{
    /** @var int At least one violation is expected */
    protected const AL_LEAST_ONE_VIOLATION = -1;

    /** @var int No violation is expected */
    protected const NO_VIOLATION = 0;

    /** @var int One violation is expected */
    protected const ONE_VIOLATION = 1;

    /**
     * Resets a changed working directory.
     */
    protected function tearDown(): void
    {
        static::returnToOriginalWorkingDirectory();
        static::cleanupTempFiles();

        parent::tearDown();
    }

    /**
     * Get a list of files that should trigger a rule violation.
     *
     * By default, files named like "testRuleAppliesTo*", but it can be overridden in sub-classes.
     *
     * @return string[]
     */
    public static function getApplyingFiles(): array
    {
        return static::getFilesForCalledClass('testRuleApplies*');
    }

    /**
     * Get a list of files that should not trigger a rule violation.
     *
     * By default, files named like "testRuleDoesNotApplyTo*", but it can be overridden in sub-classes.
     *
     * @return string[]
     */
    public static function getNotApplyingFiles(): array
    {
        return static::getFilesForCalledClass('testRuleDoesNotApply*');
    }

    /**
     * Get a list of test files specified by getApplyingFiles() as an array of 1-length arguments lists.
     *
     * @return string[][]
     */
    public static function getApplyingCases(): array
    {
        return static::getValuesAsArrays(static::getApplyingFiles());
    }

    /**
     * Get a list of test files specified by getNotApplyingFiles() as an array of 1-length arguments lists.
     *
     * @return string[][]
     */
    public static function getNotApplyingCases(): array
    {
        return static::getValuesAsArrays(static::getNotApplyingFiles());
    }

    /**
     * Returns the first class found in a source file related to the calling
     * test method.
     */
    protected function getClass(): ClassNode
    {
        return new ClassNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getClasses()
            )
        );
    }

    /**
     * Returns the first interface found in a source file related to the calling
     * test method.
     */
    protected function getInterface(): InterfaceNode
    {
        return new InterfaceNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getInterfaces()
            )
        );
    }

    protected function getTrait(): TraitNode
    {
        return new TraitNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getTraits()
            )
        );
    }

    protected function getEnum(): EnumNode
    {
        return new EnumNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getEnums()
            )
        );
    }

    /**
     * Returns the first method found in a source file related to the calling
     * test method.
     */
    protected function getMethod(): MethodNode
    {
        $source = $this->parseTestCaseSource()->getTypes()->current();
        static::assertNotFalse($source);

        return new MethodNode($this->getNodeForCallingTestCase($source->getMethods()));
    }

    /**
     * Returns the first function found in a source files related to the calling
     * test method.
     */
    protected function getFunction(): FunctionNode
    {
        return new FunctionNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getFunctions()
            )
        );
    }

    /**
     * Returns the first method or function node for a given test file.
     *
     * @since 2.8.3
     */
    protected function getNodeForTestFile(string $file): FunctionNode|MethodNode
    {
        $source = $this->parseSource($file);
        $type = $source->getTypes();
        $pathInfo = pathinfo($file, PATHINFO_FILENAME);

        if ($type->count()) {
            $source = $type->current();
            static::assertNotFalse($source);

            return new MethodNode($this->getNodeByName($source->getMethods(), $pathInfo));
        }

        return new FunctionNode($this->getNodeByName($source->getFunctions(), $pathInfo));
    }

    /**
     * Assert that a given file trigger N times the given rule.
     *
     * @param Rule $rule Rule to test.
     * @param int $expectedInvokes Count of expected invocations.
     * @param string $file Test file containing a method with the same name to be tested.
     */
    protected function expectRuleHasViolationsForFile(Rule $rule, int $expectedInvokes, string $file): void
    {
        $report = new Report();
        $rule->setReport($report);
        $rule->apply($this->getNodeForTestFile($file));
        $violations = $report->getRuleViolations();
        $actualInvokes = count($violations);
        $assertion = $expectedInvokes === self::AL_LEAST_ONE_VIOLATION
            ? $actualInvokes > 0
            : $actualInvokes === $expectedInvokes;

        static::assertTrue(
            $assertion,
            $assertion ? '' : $this->getViolationFailureMessage($file, $expectedInvokes, $actualInvokes, $violations)
        );
    }

    /**
     * Return a human-friendly failure message for a given list of violations and the actual/expected counts.
     *
     * @param iterable<RuleViolation> $violations
     */
    protected function getViolationFailureMessage(
        string $file,
        int $expectedInvokes,
        int $actualInvokes,
        iterable $violations
    ): string {
        return basename($file) . " failed:\n" .
            "Expected $expectedInvokes violation" . ($expectedInvokes !== 1 ? 's' : '') . "\n" .
            "But $actualInvokes violation" . ($actualInvokes !== 1 ? 's' : '') . ' raised' .
            (
                $actualInvokes > 0
                ? ":\n" . $this->getViolationsSummary($violations)
                : '.'
            );
    }

    /**
     * Return a human-friendly summary for a list of violations.
     *
     * @param iterable<RuleViolation> $violations
     */
    protected function getViolationsSummary(iterable $violations): string
    {
        if (!is_array($violations)) {
            $violations = iterator_to_array($violations);
        }

        return implode("\n", array_map(function (RuleViolation $violation) {
            $nodeExtractor = new ReflectionProperty(RuleViolation::class, 'node');
            $nodeExtractor->setAccessible(true);
            $value = $nodeExtractor->getValue($violation);
            $node = null;
            if ($value instanceof AbstractNode) {
                $node = $value->getNode();
            }
            $message = '  - line ' . $violation->getBeginLine();

            if ($node) {
                $type = preg_replace('/^PDepend\\\\Source\\\\AST\\\\AST/', '', $node::class);
                $message .= ' on ' . $type . ' ' . $node->getImage();
            }

            return $message;
        }, $violations));
    }

    /**
     * Returns the absolute path for a test resource for the current test.
     *
     * @since 1.1.0
     */
    protected static function createCodeResourceUriForTest(): string
    {
        $frame = static::getCallingTestCase();

        return self::createResourceUriForTest($frame['function'] . '.php');
    }

    /**
     * Returns the absolute path for a test resource for the current test.
     *
     * @param string $localPath The local/relative file location
     *
     * @since 1.1.0
     */
    protected static function createResourceUriForTest(string $localPath): string
    {
        $class = static::getCallingTestCase()['class'] ?? null;
        static::assertIsString($class);

        return static::getResourceFilePathFromClassName($class, $localPath);
    }

    /**
     * Return URI for a given pattern with directory based on the current called class name.
     */
    protected static function createResourceUriForCalledClass(string $pattern): string
    {
        return static::getResourceFilePathFromClassName(static::class, $pattern);
    }

    /**
     * Return list of files matching a given pattern with directory based on the current called class name.
     *
     * @return string[]
     */
    protected static function getFilesForCalledClass(string $pattern = '*'): array
    {
        return glob(static::createResourceUriForCalledClass($pattern)) ?: [];
    }

    /**
     * Creates a mocked class node instance.
     *
     * @return ClassNode&MockObject
     */
    protected function getClassMock(?string $metric = null, ?int $value = null)
    {
        $class = $this->getMockBuilder(ClassNode::class)
            ->setConstructorArgs([new ASTClass('FooBar')])
            ->getMock();

        if ($metric !== null) {
            $class->expects(static::atLeastOnce())
                ->method('getMetric')
                ->with(static::equalTo($metric))
                ->willReturn($value);
        }

        return $class;
    }

    /**
     * Creates a mocked method node instance.
     *
     * @return MethodNode&MockObject
     */
    protected function getMethodMock(?string $metric = null, ?int $value = null)
    {
        $method = $this->createFunctionOrMethodMock(MethodNode::class, new ASTMethod('fooBar'), $metric, $value);
        static::assertInstanceOf(MethodNode::class, $method);

        return $method;
    }

    /**
     * Creates a mocked function node instance.
     *
     * @param ?string $metric The metric acronym used by PHP_Depend.
     * @param ?numeric $value The expected metric return value.
     */
    protected function createFunctionMock(?string $metric = null, mixed $value = null): FunctionNode&MockObject
    {
        $function = $this->createFunctionOrMethodMock(
            FunctionNode::class,
            new ASTFunction('fooBar'),
            $metric,
            $value
        );
        static::assertInstanceOf(FunctionNode::class, $function);

        return $function;
    }

    /**
     * Creates a mocked report instance.
     *
     * @param int $expectedInvokes Number of expected invokes.
     * @return MockObject&Report
     */
    protected function getReportMock(int $expectedInvokes = -1)
    {
        if ($expectedInvokes === self::AL_LEAST_ONE_VIOLATION) {
            $expects = static::atLeastOnce();
        } elseif ($expectedInvokes === self::NO_VIOLATION) {
            $expects = static::never();
        } elseif ($expectedInvokes === self::ONE_VIOLATION) {
            $expects = static::once();
        } else {
            $expects = static::exactly($expectedInvokes);
        }

        $report = $this->getMockBuilder(Report::class)->getMock();
        $report->expects($expects)
            ->method('addRuleViolation');

        return $report;
    }

    /**
     * Get a mocked report with one violation
     */
    public function getReportWithOneViolation(): Report
    {
        return $this->getReportMock(self::ONE_VIOLATION);
    }

    /**
     * Get a mocked report with no violation
     *
     * @return MockObject&Report
     */
    public function getReportWithNoViolation()
    {
        return $this->getReportMock(self::NO_VIOLATION);
    }

    /**
     * Get a mocked report with at least one violation
     */
    public function getReportWithAtLeastOneViolation(): Report
    {
        return $this->getReportMock(self::AL_LEAST_ONE_VIOLATION);
    }

    /**
     * Creates a mocked {@link \PHPMD\AbstractRule} instance.
     *
     * @return AbstractRule&MockObject
     */
    protected function getRuleMock()
    {
        return @$this->getMockForAbstractClass(AbstractRule::class);
    }

    /**
     * Creates a mocked rule-set instance.
     *
     * @param ?class-string<AbstractNode<ASTNode>> $expectedClass Optional class name for apply() expected at least
     *                                                            once.
     * @param int|string $count How often should apply() be called?
     * @return MockObject&RuleSet
     */
    protected function getRuleSetMock($expectedClass = null, int|string $count = '*')
    {
        $ruleSet = $this->getMockBuilder(RuleSet::class)->getMock();
        if ($expectedClass === null) {
            $ruleSet->expects(static::never())->method('apply');

            return $ruleSet;
        }

        if ($count === '*') {
            $count = static::atLeastOnce();
        } else {
            static::assertIsInt($count);
            $count = static::exactly($count);
        }

        $ruleSet->expects($count)
            ->method('apply')
            ->with(static::isInstanceOf($expectedClass));

        return $ruleSet;
    }

    /**
     * Creates a mocked rule violation instance.
     *
     * @param string $fileName The filename to use.
     * @param int $beginLine The begin of violation line number to use.
     * @param int $endLine The end of violation line number to use.
     * @param object|null $rule The rule object to use.
     * @param string|null $description The violation description to use.
     * @return MockObject&RuleViolation
     */
    protected function getRuleViolationMock(
        string $fileName = '/foo/bar.php',
        int $beginLine = 23,
        int $endLine = 42,
        ?object $rule = null,
        ?string $description = null
    ) {
        $ruleViolation = $this->getMockBuilder(RuleViolation::class)
            ->setConstructorArgs(
                [new TooManyFields(), new NodeInfo('fileName', 'namespace', null, null, null, 1, 2), 'Hello']
            )->getMock();

        if ($rule === null) {
            $rule = new RuleStub();
        }

        if ($description === null) {
            $description = 'Test description';
        }

        $ruleViolation
            ->method('getRule')
            ->willReturn($rule);
        $ruleViolation
            ->method('getFileName')
            ->willReturn($fileName);
        $ruleViolation
            ->method('getBeginLine')
            ->willReturn($beginLine);
        $ruleViolation
            ->method('getEndLine')
            ->willReturn($endLine);
        $ruleViolation
            ->method('getNamespaceName')
            ->willReturn('TestStubPackage');
        $ruleViolation
            ->method('getDescription')
            ->willReturn($description);

        return $ruleViolation;
    }

    /**
     * Creates a mocked rule violation instance.
     *
     * @return MockObject&ProcessingError
     */
    protected function getErrorMock(
        string $file = '/foo/baz.php',
        string $message = 'Error in file "/foo/baz.php"'
    ) {
        $processingError = $this->getMockBuilder(ProcessingError::class)
            ->setConstructorArgs([$message])
            ->onlyMethods(['getFile', 'getMessage'])
            ->getMock();

        $processingError
            ->method('getFile')
            ->willReturn($file);
        $processingError
            ->method('getMessage')
            ->willReturn($message);

        return $processingError;
    }

    /**
     * Parses the source code for the calling test method and returns the first
     * package node found in the parsed file.
     */
    private function parseTestCaseSource(): ASTNamespace
    {
        return $this->parseSource($this->createCodeResourceUriForTest());
    }

    /**
     * @param class-string<FunctionNode|MethodNode> $mockBuilder
     * @param ?string $metric The metric acronym used by PHP_Depend.
     * @param ?numeric $value The expected metric return value.
     * @return (FunctionNode|MethodNode)&MockObject
     */
    private function createFunctionOrMethodMock(
        string $mockBuilder,
        ASTFunction|ASTMethod $astMock,
        ?string $metric = null,
        mixed $value = null
    ): MockObject {
        $mock = $this->getMockBuilder($mockBuilder)
            ->setConstructorArgs([$astMock])
            ->getMock();

        if ($metric !== null) {
            $mock->expects(static::atLeastOnce())
                ->method('getMetric')
                ->with(static::equalTo($metric))
                ->willReturn($value);
        }

        return $mock;
    }

    /**
     * Returns the PHP_Depend node having the given name.
     *
     * @template T of ASTNode
     * @param Iterator<T> $nodes
     * @return T
     * @throws ErrorException
     */
    private function getNodeByName(Iterator $nodes, string $name): ASTNode
    {
        foreach ($nodes as $node) {
            if ($node->getImage() === $name) {
                return $node;
            }
        }

        throw new ErrorException("Cannot locate node named $name.");
    }

    /**
     * Returns the PHP_Depend node for the calling test case.
     *
     * @template T of ASTNode
     * @param Iterator<T> $nodes
     * @return T
     * @throws ErrorException
     */
    private function getNodeForCallingTestCase(Iterator $nodes): ASTNode
    {
        $frame = $this->getCallingTestCase();

        return $this->getNodeByName($nodes, $frame['function']);
    }

    /**
     * Parses the source of the given file and returns the first package found
     * in that file.
     *
     * @throws ErrorException
     */
    private function parseSource(string $sourceFile): ASTNamespace
    {
        if (!file_exists($sourceFile)) {
            throw new ErrorException('Cannot locate source file: ' . $sourceFile);
        }

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($sourceFile);

        $builder = new PHPBuilder();

        $parser = new PHPParserGeneric(
            $tokenizer,
            $builder,
            new MemoryCacheDriver()
        );
        $parser->parse();
        $namespace = $builder->getNamespaces()->current();
        static::assertNotFalse($namespace);

        return $namespace;
    }
}
