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

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use PHPMD\Node\ClassNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\InterfaceNode;
use PHPMD\Node\MethodNode;
use PHPMD\Node\TraitNode;
use PHPMD\Rule\Design\TooManyFields;
use PHPMD\Stubs\RuleStub;
use PHPUnit_Framework_MockObject_MockBuilder;

/**
 * Abstract base class for PHPMD test cases.
 */
abstract class AbstractTest extends AbstractStaticTest
{
    /**
     * @return string[]
     */
    public function getSuccessFiles()
    {
        return $this->getFilesForCalledClass('testRuleAppliesTo*');
    }

    /**
     * @return string[]
     */
    public function getFailureFiles()
    {
        return $this->getFilesForCalledClass('testRuleDoesNotApplyTo*');
    }

    public function getSuccessCases()
    {
        return static::getValuesAsArrays($this->getSuccessFiles());
    }

    public function getFailureCases()
    {
        return static::getValuesAsArrays($this->getFailureFiles());
    }

    /**
     * Resets a changed working directory.
     *
     * @return void
     */
    protected function tearDown()
    {
        static::returnToOriginalWorkingDirectory();
        static::cleanupTempFiles();

        parent::tearDown();
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
     * Returns the first method as a MethodNode for a given test file.
     *
     * @param string $file
     * @return MethodNode
     * @since 2.8.3
     */
    protected function getMethodNodeForTestFile($file)
    {
        return new MethodNode(
            $this->getNodeByName(
                $this->parseSource($file)
                    ->getTypes()
                    ->current()
                    ->getMethods(),
                pathinfo($file, PATHINFO_FILENAME)
            )
        );
    }

    /**
     * Test that a given file trigger N times the given rule.
     *
     * @param Rule   $rule            Rule to test.
     * @param int    $expectedInvokes Count of expected invocations.
     * @param string $file            Test file containing a method with the same name to be tested.
     */
    protected function expectRuleInvokesForFile(Rule $rule, $expectedInvokes, $file)
    {
        $rule->setReport($this->getReportMock($expectedInvokes));
        $rule->apply($this->getMethodNodeForTestFile($file));
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
    protected function createResourceUriForCalledClass($pattern)
    {
        return $this->getResourceFilePathFromClassName(get_class($this), $pattern);
    }

    /**
     * Return list of files matching a given pattern with directory based on the current called class name.
     *
     * @param string $pattern
     * @return string[]
     */
    protected function getFilesForCalledClass($pattern = '*')
    {
        return glob($this->createResourceUriForCalledClass($pattern));
    }

    /**
     * Creates a mocked class node instance.
     *
     * @param string  $metric
     * @param integer $value
     * @return ClassNode
     */
    protected function getClassMock($metric = null, $value = null)
    {
        $class = $this->getMockFromBuilder(
            $this->getMockBuilder('PHPMD\\Node\\ClassNode')
                ->setConstructorArgs(array(new ASTClass('FooBar')))
        );

        if ($metric !== null) {
            $class->expects($this->atLeastOnce())
                ->method('getMetric')
                ->with($this->equalTo($metric))
                ->will($this->returnValue($value));
        }

        return $class;
    }

    /**
     * Creates a mocked method node instance.
     *
     * @param string  $metric
     * @param integer $value
     * @return MethodNode
     */
    protected function getMethodMock($metric = null, $value = null)
    {
        return $this->createFunctionOrMethodMock('PHPMD\\Node\\MethodNode', new ASTMethod('fooBar'), $metric, $value);
    }

    /**
     * Creates a mocked function node instance.
     *
     * @param string $metric The metric acronym used by PHP_Depend.
     * @param mixed  $value  The expected metric return value.
     * @return FunctionNode
     */
    protected function createFunctionMock($metric = null, $value = null)
    {
        return $this->createFunctionOrMethodMock('PHPMD\\Node\\FunctionNode', new ASTFunction('fooBar'), $metric, $value);
    }

    /**
     * Initializes the getMetric() method of the given function or method node.
     *
     * @param FunctionNode|MethodNode $mock
     * @param string $metric
     * @param mixed $value
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
            ->will($this->returnValue($value));

        return $mock;
    }

    /**
     * Creates a mocked report instance.
     *
     * @param integer $expectedInvokes Number of expected invokes.
     * @return \PHPMD\Report
     */
    protected function getReportMock($expectedInvokes = -1)
    {
        if ($expectedInvokes < 0) {
            $expects = $this->atLeastOnce();
        } elseif ($expectedInvokes === 0) {
            $expects = $this->never();
        } elseif ($expectedInvokes === 1) {
            $expects = $this->once();
        } else {
            $expects = $this->exactly($expectedInvokes);
        }

        $report = $this->getMockFromBuilder($this->getMockBuilder('PHPMD\\Report'));
        $report->expects($expects)
            ->method('addRuleViolation');

        return $report;
    }

    protected function getMockFromBuilder(PHPUnit_Framework_MockObject_MockBuilder $builder)
    {
        if (version_compare(phpversion(), '7.4.0-dev', '<')) {
            return $builder->getMock();
        }

        return @$builder->getMock();
    }

    /**
     * Creates a mocked {@link \PHPMD\AbstractRule} instance.
     *
     * @return \PHPMD\AbstractRule
     */
    protected function getRuleMock()
    {
        if (version_compare(phpversion(), '7.4.0-dev', '<')) {
            return $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        }

        return @$this->getMockForAbstractClass('PHPMD\\AbstractRule');
    }

    /**
     * Creates a mocked rule-set instance.
     *
     * @param string  $expectedClass Optional class name for apply() expected at least once.
     * @param integer $count How often should apply() be called?
     * @return RuleSet
     */
    protected function getRuleSetMock($expectedClass = null, $count = '*')
    {
        $ruleSet = $this->getMockFromBuilder($this->getMockBuilder('PHPMD\RuleSet'));
        if ($expectedClass === null) {
            $ruleSet->expects($this->never())->method('apply');
        } else {
            $ruleSet->expects(
                $count === '*' ? $this->atLeastOnce() : $this->exactly($count)
            )
                ->method('apply')
                ->with($this->isInstanceOf($expectedClass));
        }
        return $ruleSet;
    }

    /**
     * Creates a mocked rule violation instance.
     *
     * @param string $fileName The filename to use.
     * @param integer $beginLine The begin of violation line number to use.
     * @param integer $endLine The end of violation line number to use.
     * @param null|object $rule The rule object to use.
     * @param null|string $description The violation description to use.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRuleViolationMock(
        $fileName = '/foo/bar.php',
        $beginLine = 23,
        $endLine = 42,
        $rule = null,
        $description = null
    ) {
        $ruleViolation = $this->getMockFromBuilder(
            $this->getMockBuilder('PHPMD\\RuleViolation')
                ->setConstructorArgs(array(new TooManyFields(), new FunctionNode(new ASTFunction('fooBar')), 'Hello'))
        );

        if ($rule === null) {
            $rule = new RuleStub();
        }

        if ($description === null) {
            $description = 'Test description';
        }

        $ruleViolation->expects($this->any())
            ->method('getRule')
            ->will($this->returnValue($rule));
        $ruleViolation->expects($this->any())
            ->method('getFileName')
            ->will($this->returnValue($fileName));
        $ruleViolation->expects($this->any())
            ->method('getBeginLine')
            ->will($this->returnValue($beginLine));
        $ruleViolation->expects($this->any())
            ->method('getEndLine')
            ->will($this->returnValue($endLine));
        $ruleViolation->expects($this->any())
            ->method('getNamespaceName')
            ->will($this->returnValue('TestStubPackage'));
        $ruleViolation->expects($this->any())
            ->method('getDescription')
            ->will($this->returnValue($description));

        return $ruleViolation;
    }

    /**
     * Creates a mocked rul violation instance.
     *
     * @param string  $file
     * @param string  $message
     * @return \PHPMD\ProcessingError
     */
    protected function getErrorMock(
        $file = '/foo/baz.php',
        $message = 'Error in file "/foo/baz.php"') {

        $processingError = $this->getMockFromBuilder(
            $this->getMockBuilder('PHPMD\\ProcessingError')
                ->setConstructorArgs(array(null))
                ->setMethods(array('getFile', 'getMessage'))
        );

        $processingError->expects($this->any())
            ->method('getFile')
            ->will($this->returnValue($file));
        $processingError->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($message));

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
     * @param string                $mockBuilder
     * @param ASTFunction|ASTMethod $mock
     * @param string                $metric The metric acronym used by PHP_Depend.
     * @param mixed                 $value  The expected metric return value.
     * @return FunctionNode|MethodNode
     */
    private function createFunctionOrMethodMock($mockBuilder, $mock, $metric = null, $value = null)
    {
        return $this->initFunctionOrMethod(
            $this->getMockFromBuilder(
                $this->getMockBuilder($mockBuilder)
                    ->setConstructorArgs(array($mock))
            ),
            $metric,
            $value
        );
    }

    /**
     * Returns the PHP_Depend node having the given name.
     *
     * @param \Iterator $nodes
     * @return PHP_Depend_Code_AbstractItem
     * @throws \ErrorException
     */
    private function getNodeByName(\Iterator $nodes, $name)
    {
        foreach ($nodes as $node) {
            if ($node->getName() === $name) {
                return $node;
            }
        }
        throw new \ErrorException("Cannot locate node named $name.");
    }

    /**
     * Returns the PHP_Depend node for the calling test case.
     *
     * @param \Iterator $nodes
     * @return PHP_Depend_Code_AbstractItem
     * @throws \ErrorException
     */
    private function getNodeForCallingTestCase(\Iterator $nodes)
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
     * @throws \ErrorException
     */
    private function parseSource($sourceFile)
    {
        if (file_exists($sourceFile) === false) {
            throw new \ErrorException('Cannot locate source file: ' . $sourceFile);
        }

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($sourceFile);

        $builder =  new PHPBuilder();

        $parser = new PHPParserGeneric(
            $tokenizer,
            $builder,
            new MemoryCacheDriver()
        );
        $parser->parse();

        return $builder->getNamespaces()->current();
    }
}
