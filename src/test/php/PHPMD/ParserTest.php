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

use PDepend\Source\Parser\InvalidStateException;

/**
 * Test case for the PHP_Depend backend adapter class.
 *
 * @covers \PHPMD\Parser
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

        $adapter = new Parser($this->getPHPDependMock());
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

        $adapter = new Parser($this->getPHPDependMock());
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
        $adapter = new Parser($this->getPHPDependMock());
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
        $adapter = new Parser($this->getPHPDependMock());
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
        $adapter = new Parser($this->getPHPDependMock());
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
        $adapter = new Parser($this->getPHPDependMock());
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
                new InvalidStateException(42, __FILE__, 'foo')
            )));

        $parser = new Parser($pdepend);
        $parser->parse($report);
    }

    /**
     * Creates a mocked PDepend instance.
     *
     * @return \PDepend\Engine
     */
    private function getPHPDependMock()
    {
        return $this->getMock('PDepend\Engine', []);
    }

    /**
     * Creates a mocked PDepend class instance.
     *
     * @return \PDepend\Source\AST\ASTClass
     */
    protected function getPHPDependClassMock()
    {
        $class = $this->getMock('PDepend\\Source\\AST\\ASTClass', []);
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
     * @return PHP_Depend_Code_Function
     */
    protected function getPHPDependFunctionMock($fileName = '/foo/bar.php')
    {
        $function = $this->getMock('PDepend\Source\AST\ASTFunction', []);
        $function->expects($this->atLeastOnce())
            ->method('getCompilationUnit')
            ->will($this->returnValue($this->getPHPDependFileMock($fileName)));

        return $function;
    }

    /**
     * Creates a mocked PHP_Depend method instance.
     *
     * @param string $fileName Optional file name for the source file.
     * @return PHP_Depend_Code_CodeMethod
     */
    protected function getPHPDependMethodMock($fileName = '/foo/bar.php')
    {
        $method = $this->getMock('PDepend\Source\AST\ASTMethod', []);
        $method->expects($this->atLeastOnce())
            ->method('getCompilationUnit')
            ->will($this->returnValue($this->getPHPDependFileMock($fileName)));

        return $method;
    }

    /**
     * Creates a mocked PHP_Depend file instance.
     *
     * @param string $fileName The temporary file name.
     * @return PHP_Depend_Code_File
     */
    protected function getPHPDependFileMock($fileName)
    {
        $file = $this->getMock('PDepend\Source\AST\ASTCompilationUnit', []);
        $file->expects($this->any())
            ->method('getFileName')
            ->will($this->returnValue($fileName));

        return $file;
    }
}
