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
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Abstract base class for PHPMD test cases.
 */
abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Directory with test files.
     *
     * @var string $_filesDirectory
     */
    private static $filesDirectory;

    /**
     * Original directory is used to reset a changed working directory.
     *
     * @return void
     */
    private static $originalWorkingDirectory;

    /**
     * Temporary files created by a test.
     *
     * @var array(string)
     */
    private static $tempFiles = array();

    /**
     * Resets a changed working directory.
     *
     * @return void
     */
    protected function tearDown()
    {
        if (self::$originalWorkingDirectory !== null) {
            chdir(self::$originalWorkingDirectory);
        }
        self::$originalWorkingDirectory = null;

        foreach (self::$tempFiles as $tempFile) {
            unlink($tempFile);
        }
        self::$tempFiles = array();

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
     * Returns the absolute path for a test resource for the current test.
     *
     * @return string
     * @since 1.1.0
     */
    protected static function createCodeResourceUriForTest()
    {
        $frame = self::getCallingTestCase();

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
        $frame = self::getCallingTestCase();

        $regexp = '([a-z]([0-9]+)Test$)i';
        if (preg_match($regexp, $frame['class'], $match)) {
            $parts = explode('\\', $frame['class']);
            $testPath = $parts[count($parts) - 2] . '/' . $match[1];
        } else {
            $testPath = str_replace('\\', '/', substr($frame['class'], 6, -4));
        }

        return sprintf(
            '%s/../../resources/files/%s/%s',
            __DIR__,
            $testPath,
            $localPath
        );
    }

    /**
     * Parses the source code for the calling test method and returns the first
     * package node found in the parsed file.
     *
     * @return ASTNamespace
     */
    private function parseTestCaseSource()
    {
        return $this->parseSource(self::createCodeResourceUriForTest());
    }

    /**
     * Returns the trace frame of the calling test case.
     *
     * @return array
     * @throws ErrorException
     */
    private static function getCallingTestCase()
    {
        foreach (debug_backtrace() as $frame) {
            if (strpos($frame['function'], 'test') === 0) {
                return $frame;
            }
        }
        throw new ErrorException('Cannot locate calling test case.');
    }

    /**
     * Returns the PHP_Depend node for the calling test case.
     *
     * @param Iterator $nodes
     * @return mixed
     * @throws ErrorException
     */
    private function getNodeForCallingTestCase(Iterator $nodes)
    {
        $frame = self::getCallingTestCase();
        foreach ($nodes as $node) {
            if ($node->getName() === $frame['function']) {
                return $node;
            }
        }
        throw new ErrorException('Cannot locate node for test case.');
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
        if (file_exists($sourceFile) === false) {
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

    /**
     * Creates a mocked class node instance.
     *
     * @param string $metric
     * @param mixed $value
     * @return ClassNode|PHPUnit_Framework_MockObject_MockObject
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
                ->willReturn($value);
        }

        return $class;
    }

    /**
     * Creates a mocked method node instance.
     *
     * @param string $metric
     * @param mixed $value
     * @return MethodNode
     */
    protected function getMethodMock($metric = null, $value = null)
    {
        return $this->initFunctionOrMethod(
            $this->getMockFromBuilder(
                $this->getMockBuilder('PHPMD\\Node\\MethodNode')
                    ->setConstructorArgs(array(new ASTMethod('fooBar')))
            ),
            $metric,
            $value
        );
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
        return $this->initFunctionOrMethod(
            $this->getMockFromBuilder(
                $this->getMockBuilder('PHPMD\\Node\\FunctionNode')
                    ->setConstructorArgs(array(new ASTFunction('fooBar')))
            ),
            $metric,
            $value
        );
    }

    /**
     * Initializes the getMetric() method of the given function or method node.
     *
     * @param FunctionNode|MethodNode|PHPUnit_Framework_MockObject_MockObject $mock
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
            ->willReturn($value);

        return $mock;
    }

    /**
     * Creates a mocked report instance.
     *
     * @param integer $expectedInvokes Number of expected invokes.
     * @return Report|PHPUnit_Framework_MockObject_MockObject
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
        if (version_compare(PHP_VERSION, '7.4.0-dev', '<')) {
            return $builder->getMock();
        }

        return @$builder->getMock();
    }

    /**
     * Creates a mocked {@link \PHPMD\AbstractRule} instance.
     *
     * @return AbstractRule|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRuleMock()
    {
        if (version_compare(PHP_VERSION, '7.4.0-dev', '<')) {
            return $this->getMockForAbstractClass('PHPMD\\AbstractRule');
        }

        return @$this->getMockForAbstractClass('PHPMD\\AbstractRule');
    }

    /**
     * Creates a mocked rule-set instance.
     *
     * @param string $expectedClass Optional class name for apply() expected at least once.
     * @param mixed $count How often should apply() be called?
     * @return RuleSet|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRuleSetMock($expectedClass = null, $count = '*')
    {
        $ruleSet = $this->getMockFromBuilder($this->getMockBuilder('PHPMD\RuleSet'));
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
     * @param integer $beginLine The begin of violation line number to use.
     * @param integer $endLine The end of violation line number to use.
     * @param null|object $rule The rule object to use.
     * @param null|string $description The violation description to use.
     * @return PHPUnit_Framework_MockObject_MockObject
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
     * Creates a mocked rul violation instance.
     *
     * @param string $file
     * @param string $message
     * @return ProcessingError|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getErrorMock(
        $file = '/foo/baz.php',
        $message = 'Error in file "/foo/baz.php"'
    ) {

        $processingError = $this->getMockFromBuilder(
            $this->getMockBuilder('PHPMD\\ProcessingError')
                ->setConstructorArgs(array(null))
                ->setMethods(array('getFile', 'getMessage'))
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
     * Asserts the actual xml output matches against the expected file.
     *
     * @param string $actualOutput Generated xml output.
     * @param string $expectedFileName File with expected xml result.
     * @return void
     */
    public static function assertXmlEquals($actualOutput, $expectedFileName)
    {
        $actual = simplexml_load_string($actualOutput);
        // Remove dynamic timestamp and duration attribute
        if (isset($actual['timestamp'])) {
            $actual['timestamp'] = '';
        }
        if (isset($actual['duration'])) {
            $actual['duration'] = '';
        }
        if (isset($actual['version'])) {
            $actual['version'] = '@package_version@';
        }

        $expected = str_replace(
            '#{rootDirectory}',
            self::$filesDirectory,
            file_get_contents(self::createFileUri($expectedFileName))
        );

        $expected = str_replace('_DS_', DIRECTORY_SEPARATOR, $expected);

        self::assertXmlStringEqualsXmlString($expected, $actual->saveXML());
    }

    /**
     * Asserts the actual JSON output matches against the expected file.
     *
     * @param string $actualOutput Generated JSON output.
     * @param string $expectedFileName File with expected JSON result.
     *
     * @return void
     */
    public static function assertJsonEquals($actualOutput, $expectedFileName)
    {
        $actual = json_decode($actualOutput, true);
        // Remove dynamic timestamp and duration attribute
        if (isset($actual['timestamp'])) {
            $actual['timestamp'] = '';
        }
        if (isset($actual['duration'])) {
            $actual['duration'] = '';
        }
        if (isset($actual['version'])) {
            $actual['version'] = '@package_version@';
        }

        $expected = str_replace(
            '#{rootDirectory}',
            self::$filesDirectory,
            file_get_contents(self::createFileUri($expectedFileName))
        );

        $expected = str_replace('_DS_', DIRECTORY_SEPARATOR, $expected);

        self::assertJsonStringEqualsJsonString($expected, json_encode($actual));
    }

    /**
     * This method initializes the test environment, it configures the files
     * directory and sets the include_path for svn versions.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$filesDirectory = realpath(__DIR__ . '/../../resources/files');

        if (false === strpos(get_include_path(), self::$filesDirectory)) {
            set_include_path(
                sprintf(
                    '%s%s%s%s%s',
                    get_include_path(),
                    PATH_SEPARATOR,
                    self::$filesDirectory,
                    PATH_SEPARATOR,
                    realpath(__DIR__ . '/../')
                )
            );
        }

        // Prevent timezone warnings if no default TZ is set (PHP > 5.1.0)
        date_default_timezone_set('UTC');
    }

    /**
     * Changes the working directory for a single test.
     *
     * @param string $localPath The temporary working directory.
     * @return void
     */
    protected static function changeWorkingDirectory($localPath = '')
    {
        self::$originalWorkingDirectory = getcwd();

        if (0 === preg_match('(^([A-Z]:|/))', $localPath)) {
            $localPath = self::createFileUri($localPath);
        }
        chdir($localPath);
    }

    /**
     * Creates a full filename for a test content in the <em>_files</b> directory.
     *
     * @param string $localPath
     * @return string
     */
    protected static function createFileUri($localPath = '')
    {
        return self::$filesDirectory . '/' . $localPath;
    }

    /**
     * Creates a file uri for a temporary test file.
     *
     * @return string
     */
    protected static function createTempFileUri()
    {
        return (self::$tempFiles[] = tempnam(sys_get_temp_dir(), 'phpmd.'));
    }
}
