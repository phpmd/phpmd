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

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Abstract base class for PHP_PMD test cases.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://phpmd.org
 */
abstract class PHP_PMD_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Directory with test files.
     *
     * @var string $_filesDirectory
     */
    private static $_filesDirectory = null;

    /**
     * Original directory is used to reset a changed working directory.
     *
     * @return void
     */
    private static $_originalWorkingDirectory = null;

    /**
     * Temporary files created by a test.
     *
     * @var array(string)
     */
    private static $_tempFiles = array();

    /**
     * Resets a changed working directory.
     *
     * @return void
     */
    protected function tearDown()
    {
        if (self::$_originalWorkingDirectory !== null) {
            chdir(self::$_originalWorkingDirectory);
        }
        self::$_originalWorkingDirectory = null;

        foreach (self::$_tempFiles as $tempFile) {
            unlink($tempFile);
        }
        self::$_tempFiles = array();

        parent::tearDown();
    }

    /**
     * Returns the first class found in a source file related to the calling
     * test method.
     *
     * @return PHP_PMD_Node_Class
     */
    protected function getClass()
    {
        include_once 'PHP/PMD/Node/Class.php';

        return new PHP_PMD_Node_Class(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getClasses()
            )
        );
    }

    /**
     * Returns the first interface found in a source file related to the calling
     * test method.
     *
     * @return PHP_PMD_Node_Interface
     */
    protected function getInterface()
    {
        include_once 'PHP/PMD/Node/Interface.php';

        return new PHP_PMD_Node_Interface(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getInterfaces()
            )
        );
    }

    /**
     * Returns the first method found in a source file related to the calling
     * test method.
     *
     * @return PHP_PMD_Node_Method
     */
    protected function getMethod()
    {
        include_once 'PHP/PMD/Node/Method.php';

        return new PHP_PMD_Node_Method(
            $this->getNodeForCallingTestCase(
                $this->parseTestCaseSource()->getTypes()->current()->getMethods()
            )
        );
    }

    /**
     * Returns the first function found in a source files related to the calling
     * test method.
     *
     * @return PHP_PMD_Node_Function
     */
    protected function getFunction()
    {
        include_once 'PHP/PMD/Node/Function.php';

        return new PHP_PMD_Node_Function(
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
     *
     * @return string
     * @since 1.1.0
     */
    protected static function createResourceUriForTest($localPath)
    {
        $frame = self::getCallingTestCase();

        $regexp = '(_([^_]+)_[^_]+[a-z]([0-9]+)Test)i';
        if (preg_match($regexp, $frame['class'], $match)) {
            $testPath = $match[1] . '/' . $match[2];
        } else {
            $testPath = strtr(substr($frame['class'], 8, -4), '_', '/');
        }

        return sprintf(
            '%s/../../../resources/files/%s/%s',
            dirname(__FILE__),
            $testPath,
            $localPath
        );
    }

    /**
     * Parses the source code for the calling test method and returns the first
     * package node found in the parsed file.
     *
     * @return PHP_Depend_Code_Package
     */
    private function parseTestCaseSource()
    {
        return $this->parseSource($this->createCodeResourceUriForTest());
    }

    /**
     * Returns the trace frame of the calling test case.
     *
     * @return array
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
     * @param Iterator $nodes The raw input iterator.
     *
     * @return PHP_Depend_Code_AbstractItem
     */
    private function getNodeForCallingTestCase(Iterator $nodes)
    {
        $frame = $this->getCallingTestCase();
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
     * @param string $sourceFile Name of the test source file.
     *
     * @return PHP_Depend_Code_Package
     */
    private function parseSource($sourceFile)
    {
        if (file_exists($sourceFile) === false) {
            throw new ErrorException('Cannot locate source file: ' . $sourceFile);
        }

        $tokenizer = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $builder =  new PHP_Depend_Builder_Default();

        $parser = new PHP_Depend_Parser_VersionAllParser(
            $tokenizer,
            $builder,
            new PHP_Depend_Util_Cache_Driver_Memory()
        );
        $parser->parse();

        return $builder->getPackages()->current();
    }

    /**
     * Creates a mocked class node instance.
     *
     * @param string $metric The metric acronym used by PHP_Depend.
     * @param mixed  $value  The expected metric return value.
     *
     * @return PHP_PMD_Node_Class
     */
    protected function getClassMock($metric = null, $value = null)
    {
        include_once 'PHP/PMD/Node/Class.php';

        $class = $this->getMock(
            'PHP_PMD_Node_Class',
            array(),
            array(null),
            '',
            false
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
     * @param string $metric The metric acronym used by PHP_Depend.
     * @param mixed  $value  The expected metric return value.
     *
     * @return PHP_PMD_Node_Method
     */
    protected function getMethodMock($metric = null, $value = null)
    {
        include_once 'PHP/PMD/Node/Method.php';

        return $this->initFunctionOrMethod(
            $this->getMock('PHP_PMD_Node_Method', array(), array(null), '', false),
            $metric,
            $value
        );
    }

    /**
     * Creates a mocked function node instance.
     *
     * @param string $metric The metric acronym used by PHP_Depend.
     * @param mixed  $value  The expected metric return value.
     *
     * @return PHP_PMD_Node_Function
     */
    protected function createFunctionMock($metric = null, $value = null)
    {
        include_once 'PHP/PMD/Node/Function.php';

        return $this->initFunctionOrMethod(
            $this->getMock('PHP_PMD_Node_Function', array(), array(null), '', false),
            $metric,
            $value
        );
    }

    /**
     * Initializes the getMetric() method of the given function or method node.
     *
     * @param PHP_PMD_Node_Function|PHP_PMD_Node_Method $mock   Mock instance.
     * @param string                                    $metric Metric acronym.
     * @param mixed                                     $value  Expected value.
     *
     * @return PHP_PMD_Node_Function|PHP_PMD_Node_Method
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
     *
     * @return PHP_PMD_Report
     */
    protected function getReportMock($expectedInvokes = -1)
    {
        include_once 'PHP/PMD/Report.php';

        $expects = null;
        if ($expectedInvokes < 0) {
            $expects = $this->atLeastOnce();
        } else if ($expectedInvokes === 0) {
            $expects = $this->never();
        } else if ($expectedInvokes === 1) {
            $expects = $this->once();
        } else {
            $expects = $this->exactly($expectedInvokes);
        }

        $report = $this->getMock('PHP_PMD_Report');
        $report->expects($expects)
            ->method('addRuleViolation');

        return $report;
    }

    /**
     * Creates a mocked {@link PHP_PMD_AbstractRule} instance.
     *
     * @return PHP_PMD_AbstractRule
     */
    protected function getRuleMock()
    {
        include_once 'PHP/PMD/AbstractRule.php';

        return $this->getMockForAbstractClass('PHP_PMD_AbstractRule');
    }

    /**
     * Creates a mocked rule-set instance.
     *
     * @param string $expectedClass Optional class name for apply() expected at
     *                              least once.
     * @param mixed  $count         How often should apply() be called?
     *
     * @return PHP_PMD_RuleSet
     */
    protected function getRuleSetMock($expectedClass = null, $count = '*')
    {
        $ruleSet = $this->getMock('PHP_PMD_RuleSet');
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
     * Creates a mocked rul violation instance.
     *
     * @param string  $fileName  The source code filename.
     * @param integer $beginLine The first line where the violation context begins.
     * @param integer $endLine   The last line where the violation context ends.
     * @param object  $rule      A rule instance to return.
     *
     * @return PHP_PMD_RuleViolation
     */
    protected function getRuleViolationMock(
        $fileName = '/foo/bar.php',
        $beginLine = 23,
        $endLine = 42,
        $rule = null
    ) {
        include_once 'PHP/PMD/RuleViolation.php';

        $ruleViolation = $this->getMock(
            'PHP_PMD_RuleViolation',
            array(),
            array(null, null, null),
            '',
            false
        );

        if ($rule === null) {
            include_once self::createFileUri('stubs/RuleStub.php');

            $rule = new PHP_PMD_Stubs_RuleStub();
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
            ->method('getPackageName')
            ->will($this->returnValue('TestStubPackage'));
        $ruleViolation->expects($this->any())
            ->method('getDescription')
            ->will($this->returnValue('Test description'));

        return $ruleViolation;
    }

    /**
     * Asserts the actual xml output matches against the expected file.
     *
     * @param string $actualOutput     Generated xml output.
     * @param string $expectedFileName File with expected xml result.
     *
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
            self::$_filesDirectory,
            file_get_contents(self::createFileUri($expectedFileName))
        );

        self::assertXmlStringEqualsXmlString($expected, $actual->saveXML());
    }

    /**
     * This method initializes the test environment, it configures the files
     * directory and sets the include_path for svn versions.
     *
     * @return void
     */
    public static function init()
    {
        self::$_filesDirectory = realpath(
            dirname(__FILE__) . '/../../../resources/files'
        );

        // file can contain test rule implementations.
        $include = self::$_filesDirectory;

        if (is_int($index = array_search('--include-path', $_SERVER['argv']))) {
            $ext = pathinfo($_SERVER['argv'][$index + 1], PATHINFO_EXTENSION);

            if (0 === strpos($ext, 'phar')) {
                include realpath($_SERVER['argv'][$index + 1]);
            }

        } else if (strpos('@package_version@', '@package_version') === 0) {
            $include .= PATH_SEPARATOR .
                        realpath(dirname(__FILE__) . '/../../../../main/php') .
                        PATH_SEPARATOR .
                        realpath(dirname(__FILE__) . '/../../../../../lib/pdepend/src/main/php');
        }

        // Configure include path
        set_include_path($include . PATH_SEPARATOR . get_include_path());

        // Init PHP_Depend autoloader
        include_once 'PHP/Depend/Autoload.php';

        $autoload = new PHP_Depend_Autoload();
        $autoload->register();
        
        // Prevent timezone warnings if no default TZ is set (PHP > 5.1.0) 
        date_default_timezone_set('UTC');
    }

    /**
     * Changes the working directory for a single test.
     *
     * @param string $localPath The temporary working directory.
     *
     * @return void
     */
    protected static function changeWorkingDirectory($localPath = '')
    {
        self::$_originalWorkingDirectory = getcwd();

        if (0 === preg_match('(^([A-Z]:|/))', $localPath)) {
            $localPath = self::createFileUri($localPath);
        }
        chdir($localPath);
    }

    /**
     * Creates a full filename for a test content in the <em>_files</b> directory.
     *
     * @param string $localPath The local path within the <em>_files</b> dir.
     *
     * @return string
     */
    protected static function createFileUri($localPath = '')
    {
        return self::$_filesDirectory . '/' . $localPath;
    }

    /**
     * Creates a file uri for a temporary test file.
     *
     * @return string
     */
    protected static function createTempFileUri()
    {
        return (self::$_tempFiles[] = tempnam(sys_get_temp_dir(), 'phpmd.'));
    }
}

/**
 * Init the test environment.
 */
PHP_PMD_AbstractTest::init();
