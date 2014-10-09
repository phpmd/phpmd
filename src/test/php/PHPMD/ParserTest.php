<?php
/**
 * This file is part of PHP Mess Detector.
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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD;

/**
 * Test case for the PHP_Depend backend adapter class.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Parser
 * @group phpmd
 * @group unittest
 */
class ParserTest extends AbstractTest
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

        $adapter = new \PHPMD\Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock('PHPMD\\Node\\ClassNode'));
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

        $adapter = new \PHPMD\Parser($this->getPHPDependMock());
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
        $adapter = new \PHPMD\Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock('PHPMD\\Node\\MethodNode'));
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
        $adapter = new \PHPMD\Parser($this->getPHPDependMock());
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
        $adapter = new \PHPMD\Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock('PHPMD\\Node\\FunctionNode'));
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
        $adapter = new \PHPMD\Parser($this->getPHPDependMock());
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
                new \PDepend\Source\Parser\InvalidStateException(42, __FILE__, 'foo')
            )));

        $parser = new \PHPMD\Parser($pdepend);
        $parser->parse($report);
    }

    /**
     * Creates a mocked PDepend instance.
     *
     * @return \PDepend\Engine
     */
    private function getPHPDependMock()
    {
        return $this->getMock('PDepend\Engine', array(), array(null), '', false);
    }

    /**
     * Creates a mocked PDepend class instance.
     *
     * @return PDepend\Source\AST\ASTClass
     */
    protected function getPHPDependClassMock()
    {
        $class = $this->getMock('PDepend\\Source\\AST\\ASTClass', array(), array(null));
        $class->expects($this->any())
            ->method('getCompilationUnit')
            ->will($this->returnValue($this->getPHPDependFileMock('foo.php')));
        $class->expects($this->any())
            ->method('getConstants')
            ->will($this->returnValue(new \ArrayIterator(array())));
        $class->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue(new \ArrayIterator(array())));
        $class->expects($this->any())
            ->method('getMethods')
            ->will($this->returnValue(new \ArrayIterator(array())));

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
        $function = $this->getMock('PDepend\Source\AST\ASTFunction', array(), array(null));
        $function->expects($this->atLeastOnce())
            ->method('getCompilationUnit')
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
        $method = $this->getMock('PDepend\Source\AST\ASTMethod', array(), array(null));
        $method->expects($this->atLeastOnce())
            ->method('getCompilationUnit')
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
        $file = $this->getMock('PDepend\Source\AST\ASTCompilationUnit', array(), array(null));
        $file->expects($this->any())
            ->method('getFileName')
            ->will($this->returnValue($fileName));

        return $file;
    }
}
