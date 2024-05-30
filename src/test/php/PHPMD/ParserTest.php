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

use PDepend\Engine;
use PDepend\Metrics\AnalyzerFactory;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\Parser\InvalidStateException;
use PDepend\Util\Cache\CacheFactory;
use PDepend\Util\Configuration;
use PHPMD\Node\ClassNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\MethodNode;
use Symfony\Component\DependencyInjection\Container;

/**
 * Test case for the PHP_Depend backend adapter class.
 *
 * @covers \PHPMD\Parser
 */
class ParserTest extends AbstractTestCase
{
    /**
     * Tests that the metrics adapter delegates a node to a registered rule-set.
     */
    public function testAdapterDelegatesClassNodeToRuleSet(): void
    {
        $mock = $this->getPHPDependClassMock();
        $mock->expects(static::once())
            ->method('isUserDefined')
            ->will(static::returnValue(true));

        $adapter = new Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock(ClassNode::class));
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitClass($mock);
    }

    /**
     * Tests that the metrics adapter does not delegate a node without source
     * code file to a registered rule-set.
     */
    public function testAdapterDoesNotDelegateNonSourceClassNodeToRuleSet(): void
    {
        $mock = $this->getPHPDependClassMock();
        $mock->expects(static::once())
            ->method('isUserDefined')
            ->will(static::returnValue(false));

        $adapter = new Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitClass($mock);
    }

    /**
     * Tests that the metrics adapter delegates a node to a registered rule-set.
     */
    public function testAdapterDelegatesMethodNodeToRuleSet(): void
    {
        $adapter = new Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock(MethodNode::class));
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitMethod($this->getPHPDependMethodMock());
    }

    /**
     * Tests that the metrics adapter does not delegate a node without source
     * code file to a registered rule-set.
     */
    public function testAdapterDoesNotDelegateNonSourceMethodNodeToRuleSet(): void
    {
        $adapter = new Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitMethod($this->getPHPDependMethodMock(null));
    }

    /**
     * Tests that the metrics adapter delegates a node to a registered rule-set.
     */
    public function testAdapterDelegatesFunctionNodeToRuleSet(): void
    {
        $adapter = new Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock(FunctionNode::class));
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitFunction($this->getPHPDependFunctionMock());
    }

    /**
     * Tests that the metrics adapter does not delegate a node without source
     * code file to a registered rule-set.
     */
    public function testAdapterDoesNotDelegateNonSourceFunctionNodeToRuleSet(): void
    {
        $adapter = new Parser($this->getPHPDependMock());
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitFunction($this->getPHPDependFunctionMock(null));
    }

    /**
     * testParserStoreParsingExceptionsInReport
     *
     * @since 1.2.1
     */
    public function testParserStoreParsingExceptionsInReport(): void
    {
        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('addError');

        $pdepend = $this->getPHPDependMock();
        $pdepend->expects(static::once())
            ->method('getExceptions')
            ->will(static::returnValue([
                new InvalidStateException(42, __FILE__, 'foo'),
            ]));

        $parser = new Parser($pdepend);
        $parser->parse($report);
    }

    /**
     * Creates a mocked PDepend instance.
     *
     * @return Engine
     */
    private function getPHPDependMock()
    {
        $container = new Container();
        $config = new Configuration((object) []);

        return $this->getMockFromBuilder(
            $this->getMockBuilder(Engine::class)
                ->setConstructorArgs([
                    $config,
                    new CacheFactory($config),
                    new AnalyzerFactory($container),
                ]),
        );
    }

    /**
     * Creates a mocked PDepend class instance.
     *
     * @return ASTClass
     */
    protected function getPHPDependClassMock()
    {
        $class = $this->getMockFromBuilder(
            $this->getMockBuilder(ASTClass::class)
                ->setConstructorArgs([null])
        );
        $class->expects(static::any())
            ->method('getCompilationUnit')
            ->will(static::returnValue($this->getPHPDependFileMock('foo.php')));
        $class->expects(static::any())
            ->method('getConstants')
            ->will(static::returnValue([]));
        $class->expects(static::any())
            ->method('getProperties')
            ->will(static::returnValue(new ASTArtifactList([])));
        $class->expects(static::any())
            ->method('getMethods')
            ->will(static::returnValue(new ASTArtifactList([])));

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
        $function = $this->getMockFromBuilder(
            $this->getMockBuilder(ASTFunction::class)
                ->setConstructorArgs([null])
        );
        $function->expects(static::atLeastOnce())
            ->method('getCompilationUnit')
            ->will(static::returnValue($this->getPHPDependFileMock($fileName)));

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
        $method = $this->getMockFromBuilder(
            $this->getMockBuilder(ASTMethod::class)
                ->setConstructorArgs([null])
        );
        $method->expects(static::atLeastOnce())
            ->method('getCompilationUnit')
            ->will(static::returnValue($this->getPHPDependFileMock($fileName)));

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
        $file = $this->getMockFromBuilder(
            $this->getMockBuilder(ASTCompilationUnit::class)
                ->setConstructorArgs([null])
        );
        $file->expects(static::any())
            ->method('getFileName')
            ->will(static::returnValue($fileName));

        return $file;
    }
}
