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

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/PMD/Parser.php';

/**
 * Test case for the PHP_Depend backend adapter class.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://phpmd.org
 *
 * @covers PHP_PMD_Parser
 * @group phpmd
 * @group unittest
 */
class PHP_PMD_ParserTest extends PHP_PMD_AbstractTest
{
    /**
     * Tests that the metrics adapter delegates a node to a registered rule-set.
     *
     * @return void
     */
    public function testAdapterDelegatesClassNodeToRuleSet()
    {
        $mock = $this->getPHPDependClassMock();
        $mock->expects($this->once())
            ->method('isUserDefined')
            ->will($this->returnValue(true));

        $adapter = new PHP_PMD_Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock('PHP_PMD_Node_Class'));
        $adapter->setReport($this->getReportMock(0));
        $adapter->visitClass($mock);
    }

    /**
     * Tests that the metrics adapter does not delegate a node without source
     * code file to a registered rule-set.
     *
     * @return void
     */
    public function testAdapterDoesNotDelegateNonSourceClassNodeToRuleSet()
    {
        $mock = $this->getPHPDependClassMock();
        $mock->expects($this->once())
            ->method('isUserDefined')
            ->will($this->returnValue(false));

        $adapter = new PHP_PMD_Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportMock(0));
        $adapter->visitClass($mock);
    }

    /**
     * Tests that the metrics adapter delegates a node to a registered rule-set.
     *
     * @return void
     */
    public function testAdapterDelegatesMethodNodeToRuleSet()
    {
        $adapter = new PHP_PMD_Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock('PHP_PMD_Node_Method'));
        $adapter->setReport($this->getReportMock(0));
        $adapter->visitMethod($this->getPHPDependMethodMock());
    }

    /**
     * Tests that the metrics adapter does not delegate a node without source
     * code file to a registered rule-set.
     *
     * @return void
     */
    public function testAdapterDoesNotDelegateNonSourceMethodNodeToRuleSet()
    {
        $adapter = new PHP_PMD_Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportMock(0));
        $adapter->visitMethod($this->getPHPDependMethodMock(null));
    }

    /**
     * Tests that the metrics adapter delegates a node to a registered rule-set.
     *
     * @return void
     */
    public function testAdapterDelegatesFunctionNodeToRuleSet()
    {
        $adapter = new PHP_PMD_Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock('PHP_PMD_Node_Function'));
        $adapter->setReport($this->getReportMock(0));
        $adapter->visitFunction($this->getPHPDependFunctionMock());
    }

    /**
     * Tests that the metrics adapter does not delegate a node without source
     * code file to a registered rule-set.
     *
     * @return void
     */
    public function testAdapterDoesNotDelegateNonSourceFunctionNodeToRuleSet()
    {
        $adapter = new PHP_PMD_Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportMock(0));
        $adapter->visitFunction($this->getPHPDependFunctionMock(null));
    }

    /**
     * testParserStoreParsingExceptionsInReport
     *
     * @return void
     * @since 1.2.1
     */
    public function testParserStoreParsingExceptionsInReport()
    {
        $report = $this->getReportMock(0);
        $report->expects($this->once())
            ->method('addError');

        $pdepend = $this->getPHPDependMock();
        $pdepend->expects($this->once())
            ->method('getExceptions')
            ->will($this->returnValue(array(
                new PHP_Depend_Parser_InvalidStateException(42, __FILE__, 'foo')
            )));

        $parser = new PHP_PMD_Parser($pdepend);
        $parser->parse($report);
    }

    /**
     * Creates a mocked PHP_Depend instance.
     *
     * @return PHP_Depend
     */
    private function getPHPDependMock()
    {
        return $this->getMock('PHP_Depend', array(), array(null), '', false);
    }

    /**
     * Creates a mocked PHP_Depend class instance.
     *
     * @return PHP_Depend_Code_Class
     */
    protected function getPHPDependClassMock()
    {
        $class = $this->getMock('PHP_Depend_Code_Class', array(), array(null));
        $class->expects($this->any())
            ->method('getSourceFile')
            ->will($this->returnValue($this->getPHPDependFileMock('foo.php')));
        $class->expects($this->any())
            ->method('getConstants')
            ->will($this->returnValue(new ArrayIterator(array())));
        $class->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue(new ArrayIterator(array())));
        $class->expects($this->any())
            ->method('getMethods')
            ->will($this->returnValue(new ArrayIterator(array())));

        return $class;
    }

    /**
     * Creates a mocked PHP_Depend function instance.
     *
     * @param string $fileName Optional file name for the source file.
     *
     * @return PHP_Depend_Code_Function
     */
    protected function getPHPDependFunctionMock($fileName = '/foo/bar.php')
    {
        $function = $this->getMock('PHP_Depend_Code_Function', array(), array(null));
        $function->expects($this->atLeastOnce())
            ->method('getSourceFile')
            ->will($this->returnValue($this->getPHPDependFileMock($fileName)));

        return $function;
    }

    /**
     * Creates a mocked PHP_Depend method instance.
     *
     * @param string $fileName Optional file name for the source file.
     *
     * @return PHP_Depend_Code_CodeMethod
     */
    protected function getPHPDependMethodMock($fileName = '/foo/bar.php')
    {
        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null));
        $method->expects($this->atLeastOnce())
            ->method('getSourceFile')
            ->will($this->returnValue($this->getPHPDependFileMock($fileName)));

        return $method;
    }

    /**
     * Creates a mocked PHP_Depend file instance.
     *
     * @param string $fileName The temporary file name.
     *
     * @return PHP_Depend_Code_File
     */
    protected function getPHPDependFileMock($fileName)
    {
        $file = $this->getMock('PHP_Depend_Code_File', array(), array(null));
        $file->expects($this->any())
            ->method('getFileName')
            ->will($this->returnValue($fileName));

        return $file;
    }
}
