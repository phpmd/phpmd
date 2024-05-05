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
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;
use Traversable;

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
    public static function getApplyingFiles()
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
    public static function getNotApplyingFiles()
    {
        return static::getFilesForCalledClass('testRuleDoesNotApply*');
    }

    /**
     * Get a list of test files specified by getApplyingFiles() as an array of 1-length arguments lists.
     *
     * @return string[][]
     */
    public static function getApplyingCases()
    {
        return static::getValuesAsArrays(static::getApplyingFiles());
    }

    /**
     * Get a list of test files specified by getNotApplyingFiles() as an array of 1-length arguments lists.
     *
     * @return string[][]
     */
    public static function getNotApplyingCases()
    {
        return static::getValuesAsArrays(static::getNotApplyingFiles());
    }

    /**
     * Returns the first class found in a source file related to the calling
     * test method.
     *
     * @return ClassNode
     */
    protected function getClass()
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
     *
     * @return InterfaceNode
     */
    protected function getInterface()
    {
        return new InterfaceNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getInterfaces()
            )
        );
    }

    /**
     * @return TraitNode
     */
    protected function getTrait()
    {
        return new TraitNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getTraits()
            )
        );
    }

    /**
     * @return EnumNode
     */
    protected function getEnum()
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
     *
     * @return MethodNode
     */
    protected function getMethod()
    {
        return new MethodNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()
                    ->getTypes()
                    ->current()
                    ->getMethods()
            )
        );
    }

    /**
     * Returns the first function found in a source files related to the calling
     * test method.
     *
     * @return FunctionNode
     */
    protected function getFunction()
    {
        return new FunctionNode(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getFunctions()
            )
        );
    }

    /**
     * Returns the first class found for a given test file.
     *
     * @return ClassNode
     */
    protected function getClassNodeForTestFile($file)
    {
        return new ClassNode(
            $this->parseSource($file)
                ->getTypes()
                ->current()
        );
    }

    /**
     * Returns the first method or function node for a given test file.
     *
     * @param string $file
     * @return FunctionNode|MethodNode
     * @since 2.8.3
     */
    protected function getNodeForTestFile($file)
    {
        $source = $this->parseSource($file);
        $type = $source
            ->getTypes();
        $nodeClassName = FunctionNode::class;
        $getter = 'getFunctions';

        if ($type->count()) {
            $source = $type->current();
            $nodeClassName = MethodNode::class;
            $getter = 'getMethods';
        }

        return new $nodeClassName(
            $this->getNodeByName(
                $source->{$getter}(),
                pathinfo($file, PATHINFO_FILENAME)
            )
        );
    }

    /**
     * Assert that a given file trigger N times the given rule.
     *
     * Rethrows the PHPUnit ExpectationFailedException with the base name
     * of the file for better readability.
     *
     * @param Rule $rule Rule to test.
     * @param int $expectedInvokes Count of expected invocations.
     * @param string $file Test file containing a method with the same name to be tested.
     * @return void
     * @throws ExpectationFailedException
     */
    protected function expectRuleHasViolationsForFile(Rule $rule, $expectedInvokes, $file)
    {
        $report = new Report();
        $rule->setReport($report);
        $rule->apply($this->getNodeForTestFile($file));
        $violations = $report->getRuleViolations();
        $actualInvokes = count($violations);
        $assertion = $expectedInvokes === self::AL_LEAST_ONE_VIOLATION
            ? $actualInvokes > 0
            : $actualInvokes === $expectedInvokes;

        if (!$assertion) {
            throw new ExpectationFailedException(
                $this->getViolationFailureMessage($file, $expectedInvokes, $actualInvokes, $violations)
            );
        }

        $this->assertTrue($assertion);
    }

    /**
     * Return a human-friendly failure message for a given list of violations and the actual/expected counts.
     *
     * @param string $file
     * @param int $expectedInvokes
     * @param int $actualInvokes
     * @param array|iterable|Traversable $violations
     *
     * @return string
     */
    protected function getViolationFailureMessage($file, $expectedInvokes, $actualInvokes, $violations)
    {
        return basename($file) . " failed:\n" .
            "Expected $expectedInvokes violation" . ($expectedInvokes !== 1 ? 's' : '') . "\n" .
            "But $actualInvokes violation" . ($actualInvokes !== 1 ? 's' : '') . " raised" .
            (
                $actualInvokes > 0
                ? ":\n" . $this->getViolationsSummary($violations)
                : '.'
            );
    }

    /**
     * Return a human-friendly summary for a list of violations.
     *
     * @param array|iterable|Traversable $violations
     * @return string
     */
    protected function getViolationsSummary($violations)
    {
        if (!is_array($violations)) {
            $violations = iterator_to_array($violations);
        }

        return implode("\n", array_map(function (RuleViolation $violation) {
            $nodeExtractor = new ReflectionProperty(RuleViolation::class, 'node');
            $nodeExtractor->setAccessible(true);
            $node = $nodeExtractor->getValue($violation);
            $node = $node ? $node->getNode() : null;
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
     * @return string
     * @since 1.1.0
     */
    protected static function createCodeResourceUriForTest()
    {
        $frame = static::getCallingTestCase();

        return self::createResourceUriForTest($frame['function'] . '.php');
    }

    /**
     * Returns the absolute path for a test resource for the current test.
     *
     * @param string $localPath The local/relative file location
     * @return string
     * @since 1.1.0
     */
    protected static function createResourceUriForTest($localPath)
    {
        $frame = static::getCallingTestCase();

        return static::getResourceFilePathFromClassName($frame['class'], $localPath);
    }

    /**
     * Return URI for a given pattern with directory based on the current called class name.
     *
     * @param string $pattern
     * @return string
     */
    protected static function createResourceUriForCalledClass($pattern)
    {
        return static::getResourceFilePathFromClassName(static::class, $pattern);
    }

    /**
     * Return list of files matching a given pattern with directory based on the current called class name.
     *
     * @param string $pattern
     * @return string[]
     */
    protected static function getFilesForCalledClass($pattern = '*')
    {
        return glob(static::createResourceUriForCalledClass($pattern));
    }

    /**
     * Creates a mocked class node instance.
     *
     * @param string $metric
     * @param int $value
     * @return ClassNode
     */
    protected function getClassMock($metric = null, $value = null)
    {
        $class = $this->getMockFromBuilder(
            $this->getMockBuilder(ClassNode::class)
                ->setConstructorArgs([new ASTClass('FooBar')])
        );

        if ($metric !== null) {
            $class->expects($this->atLeastOnce())
                ->method('getMetric')
                ->with($this->equalTo($metric))
                ->willReturn($value);
        }

        return $class;
    }

    /**
     * Creates a mocked method node instance.
     *
     * @param string $metric
     * @param int $value
     * @return MethodNode
     */
    protected function getMethodMock($metric = null, $value = null)
    {
        return $this->createFunctionOrMethodMock(MethodNode::class, new ASTMethod('fooBar'), $metric, $value);
    }

    /**
     * Creates a mocked function node instance.
     *
     * @param string $metric The metric acronym used by PHP_Depend.
     * @param mixed $value The expected metric return value.
     * @return FunctionNode
     */
    protected function createFunctionMock($metric = null, $value = null)
    {
        return $this->createFunctionOrMethodMock(
            FunctionNode::class,
            new ASTFunction('fooBar'),
            $metric,
            $value
        );
    }

    /**
     * Initializes the getMetric() method of the given function or method node.
     *
     * @param FunctionNode|MethodNode|PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $metric
     * @return FunctionNode|MethodNode
     */
    protected function initFunctionOrMethod($mock, $metric, $value)
    {
        if ($metric === null) {
            return $mock;
        }

        $mock->expects($this->atLeastOnce())
            ->method('getMetric')
            ->with($this->equalTo($metric))
            ->willReturn($value);

        return $mock;
    }

    /**
     * Creates a mocked report instance.
     *
     * @param int $expectedInvokes Number of expected invokes.
     * @return PHPUnit_Framework_MockObject_MockObject|Report
     */
    protected function getReportMock($expectedInvokes = -1)
    {
        if ($expectedInvokes === self::AL_LEAST_ONE_VIOLATION) {
            $expects = $this->atLeastOnce();
        } elseif ($expectedInvokes === self::NO_VIOLATION) {
            $expects = $this->never();
        } elseif ($expectedInvokes === self::ONE_VIOLATION) {
            $expects = $this->once();
        } else {
            $expects = $this->exactly($expectedInvokes);
        }

        $report = $this->getMockFromBuilder($this->getMockBuilder(Report::class));
        $report->expects($expects)
            ->method('addRuleViolation');

        return $report;
    }

    /**
     * Get a mocked report with one violation
     *
     * @return Report
     */
    public function getReportWithOneViolation()
    {
        return $this->getReportMock(self::ONE_VIOLATION);
    }

    /**
     * Get a mocked report with no violation
     *
     * @return Report
     */
    public function getReportWithNoViolation()
    {
        return $this->getReportMock(self::NO_VIOLATION);
    }

    /**
     * Get a mocked report with at least one violation
     *
     * @return Report
     */
    public function getReportWithAtLeastOneViolation()
    {
        return $this->getReportMock(self::AL_LEAST_ONE_VIOLATION);
    }

    protected function getMockFromBuilder(MockBuilder $builder)
    {
        return $builder->getMock();
    }

    /**
     * Creates a mocked {@link \PHPMD\AbstractRule} instance.
     *
     * @return AbstractRule|MockObject
     */
    protected function getRuleMock()
    {
        if (version_compare(PHP_VERSION, '7.4.0-dev', '<')) {
            return $this->getMockForAbstractClass(AbstractRule::class);
        }

        return @$this->getMockForAbstractClass(AbstractRule::class);
    }

    /**
     * Creates a mocked rule-set instance.
     *
     * @param class-string<ASTNode> $expectedClass Optional class name for apply() expected at least once.
     * @param int|string $count How often should apply() be called?
     * @return MockObject|RuleSet
     */
    protected function getRuleSetMock($expectedClass = null, $count = '*')
    {
        $ruleSet = $this->getMockFromBuilder($this->getMockBuilder(RuleSet::class));
        if ($expectedClass === null) {
            $ruleSet->expects($this->never())->method('apply');

            return $ruleSet;
        }

        if ($count === '*') {
            $count = $this->atLeastOnce();
        } else {
            $count = $this->exactly($count);
        }

        $ruleSet->expects($count)
            ->method('apply')
            ->with($this->isInstanceOf($expectedClass));

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
     * @return MockObject
     */
    protected function getRuleViolationMock(
        $fileName = '/foo/bar.php',
        $beginLine = 23,
        $endLine = 42,
        $rule = null,
        $description = null
    ) {
        $ruleViolation = $this->getMockFromBuilder(
            $this->getMockBuilder(RuleViolation::class)
                ->setConstructorArgs(
                    [new TooManyFields(), new NodeInfo('fileName', 'namespace', null, null, null, 1, 2), 'Hello']
                )
        );

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
     * @param string $file
     * @param string $message
     * @return MockObject|ProcessingError
     */
    protected function getErrorMock(
        $file = '/foo/baz.php',
        $message = 'Error in file "/foo/baz.php"'
    ) {

        $processingError = $this->getMockFromBuilder(
            $this->getMockBuilder(ProcessingError::class)
                ->setConstructorArgs([$message])
                ->onlyMethods(['getFile', 'getMessage'])
        );

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
     *
     * @return ASTNamespace
     */
    private function parseTestCaseSource()
    {
        return $this->parseSource($this->createCodeResourceUriForTest());
    }

    /**
     * @param string $mockBuilder
     * @param ASTFunction|ASTMethod $mock
     * @param string $metric The metric acronym used by PHP_Depend.
     * @param mixed $value The expected metric return value.
     * @return FunctionNode|MethodNode
     */
    private function createFunctionOrMethodMock($mockBuilder, $mock, $metric = null, $value = null)
    {
        return $this->initFunctionOrMethod(
            $this->getMockFromBuilder(
                $this->getMockBuilder($mockBuilder)
                    ->setConstructorArgs([$mock])
            ),
            $metric,
            $value
        );
    }

    /**
     * Returns the PHP_Depend node having the given name.
     *
     * @return PHP_Depend_Code_AbstractItem
     * @throws ErrorException
     */
    private function getNodeByName(Iterator $nodes, $name)
    {
        foreach ($nodes as $node) {
            if ($node->getName() === $name) {
                return $node;
            }
        }
        throw new ErrorException("Cannot locate node named $name.");
    }

    /**
     * Returns the PHP_Depend node for the calling test case.
     *
     * @return PHP_Depend_Code_AbstractItem
     * @throws ErrorException
     */
    private function getNodeForCallingTestCase(Iterator $nodes)
    {
        $frame = $this->getCallingTestCase();

        return $this->getNodeByName($nodes, $frame['function']);
    }

    /**
     * Parses the source of the given file and returns the first package found
     * in that file.
     *
     * @param string $sourceFile
     * @return ASTNamespace
     * @throws ErrorException
     */
    private function parseSource($sourceFile)
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

        return $builder->getNamespaces()->current();
    }
}
