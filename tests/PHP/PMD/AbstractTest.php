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
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/pmd
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Abstract base class for PHP_PMD test cases.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/pmd
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

        parent::tearDown();
    }

    /**
     * Creates a mocked class node instance.
     *
     * @param string $metric The metric acronym used by PHP_Depend.
     * @param mixed  $value  The expected metric return value.
     *
     * @return PHP_PMD_Node_Class
     */
    protected function getClassMock($metric, $value = null)
    {
        include_once 'PHP/PMD/Node/Class.php';

        $class = $this->getMock('PHP_PMD_Node_Class', array(), array(null), '', false);
        $class->expects($this->atLeastOnce())
              ->method('getMetric')
              ->with($this->equalTo($metric))
              ->will($this->returnValue($value));

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
    protected function getMethodMock($metric, $value = null)
    {
        include_once 'PHP/PMD/Node/Method.php';

        $method = $this->getMock('PHP_PMD_Node_Method', array(), array(null), '', false);
        $method->expects($this->atLeastOnce())
               ->method('getMetric')
               ->with($this->equalTo($metric))
               ->will($this->returnValue($value));

        return $method;
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
        $method = $report->expects($expects)
                         ->method('addRuleViolation');

        if ($expectedInvokes !== 0) {
            $method->with($this->isInstanceOf('PHP_PMD_RuleViolation'));
        }

        return $report;
    }

    /**
     * Creates a mocked rule-set instance.
     *
     * @param string $expectedClass Optional class name for apply() expected at
     *                              least once.
     *
     * @return PHP_PMD_RuleSet
     */
    protected function getRuleSetMock($expectedClass = null)
    {
        $ruleSet = $this->getMock('PHP_PMD_RuleSet');
        if ($expectedClass === null) {
            $ruleSet->expects($this->never())->method('apply');
        } else {
            $ruleSet->expects($this->atLeastOnce())
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
    protected function getRuleViolationMock($fileName = '/foo/bar.php',
                                            $beginLine = 23,
                                            $endLine = 42,
                                            $rule = null)
    {
        include_once 'PHP/PMD/RuleViolation.php';

        $ruleViolation = $this->getMock('PHP_PMD_RuleViolation', 
                                         array(),
                                         array(null, null, null),
                                         '',
                                         false);

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

        return $ruleViolation;
    }

    /**
     * This method initializes the test environment, it configures the files
     * directory and sets the include_path for svn versions.
     *
     * @return void
     */
    public static function init()
    {
        self::$_filesDirectory = dirname(__FILE__) . '/_files';

        // file can contain test rule implementations.
        $include = self::$_filesDirectory;

        // Check pear installation
        if (strpos('@package_version@', '@package_version') === 0) {
            $include .= PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../../');
        }

        // Configure include path
        set_include_path(get_include_path() . PATH_SEPARATOR . $include);

        // Include PHP_PMD main file to get the whitelist directory
        include_once 'PHP/PMD.php';
        $ref = new ReflectionClass('PHP_PMD');

        // Set source whitelist
        PHPUnit_Util_Filter::addDirectoryToWhitelist(dirname($ref->getFileName()));
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

        chdir(self::createFileUri($localPath));
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
}

/**
 * Init the test environment.
 */
PHP_PMD_AbstractTest::init();
?>
