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
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Cache\ResultCacheFileFilter;
use PHPMD\Node\ClassNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\MethodNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Container;

/**
 * Test case for the PHP_Depend backend adapter class.
 */
#[CoversClass(Parser::class)]
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
            ->willReturn(true);

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
            ->willReturn(false);

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
            ->willReturn([
                new InvalidStateException(42, __FILE__, 'foo'),
            ]);

        $parser = new Parser($pdepend);
        $parser->parse($report);
    }

    /**
     * A file whose violations are still in the result cache is parsed, so the types it declares stay
     * resolvable for other files, but its rules must not run again.
     */
    public function testAdapterDoesNotDelegateClassNodeFromResultCacheToRuleSet(): void
    {
        $mock = $this->getPHPDependClassMock(__FILE__);
        $mock->expects(static::any())
            ->method('isUserDefined')
            ->willReturn(true);

        $adapter = new Parser($this->getPHPDependMock(), $this->getResultCacheFileFilter(false));
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitClass($mock);
    }

    public function testAdapterDoesNotDelegateMethodNodeFromResultCacheToRuleSet(): void
    {
        $adapter = new Parser($this->getPHPDependMock(), $this->getResultCacheFileFilter(false));
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitMethod($this->getPHPDependMethodMock(__FILE__));
    }

    public function testAdapterDoesNotDelegateFunctionNodeFromResultCacheToRuleSet(): void
    {
        $adapter = new Parser($this->getPHPDependMock(), $this->getResultCacheFileFilter(false));
        $adapter->addRuleSet($this->getRuleSetMock());
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitFunction($this->getPHPDependFunctionMock(__FILE__));
    }

    public function testAdapterDelegatesModifiedFileNodesToRuleSet(): void
    {
        $adapter = new Parser($this->getPHPDependMock(), $this->getResultCacheFileFilter(true));
        $adapter->addRuleSet($this->getRuleSetMock(MethodNode::class));
        $adapter->setReport($this->getReportWithNoViolation());
        $adapter->visitMethod($this->getPHPDependMethodMock(__FILE__));
    }

    /**
     * Creates a result cache file filter whose state reports every file as modified or as unmodified.
     */
    private function getResultCacheFileFilter(bool $modified): ResultCacheFileFilter
    {
        $key = $this->getMockBuilder(ResultCacheKey::class)->disableOriginalConstructor()->getMock();
        $state = $this->getMockBuilder(ResultCacheState::class)->disableOriginalConstructor()->getMock();
        $state->method('isFileModified')->willReturn($modified);

        return new ResultCacheFileFilter(new NullOutput(), __DIR__, ResultCacheStrategy::Timestamp, $key, $state);
    }

    /**
     * Creates a mocked PDepend instance.
     *
     * @return Engine&MockObject
     */
    private function getPHPDependMock()
    {
        $container = new Container();
        $config = new Configuration((object) []);

        return $this->getMockBuilder(Engine::class)
            ->setConstructorArgs([
                $config,
                new CacheFactory($config),
                new AnalyzerFactory($container),
            ])->getMock();
    }

    /**
     * Creates a mocked PDepend class instance.
     *
     * @return ASTClass&MockObject
     */
    protected function getPHPDependClassMock(string $fileName = 'foo.php')
    {
        $class = $this->getMockBuilder(ASTClass::class)
            ->setConstructorArgs([null])
            ->getMock();
        $class->expects(static::any())
            ->method('getCompilationUnit')
            ->willReturn($this->getPHPDependFileMock($fileName));
        $class->expects(static::any())
            ->method('getConstants')
            ->willReturn([]);
        $class->expects(static::any())
            ->method('getProperties')
            ->willReturn(new ASTArtifactList([]));
        $class->expects(static::any())
            ->method('getMethods')
            ->willReturn(new ASTArtifactList([]));

        return $class;
    }

    /**
     * Creates a mocked PHP_Depend function instance.
     *
     * @param ?string $fileName Optional file name for the source file.
     */
    protected function getPHPDependFunctionMock(?string $fileName = '/foo/bar.php'): ASTFunction&MockObject
    {
        $function = $this->getMockBuilder(ASTFunction::class)
            ->setConstructorArgs([null])
            ->getMock();
        $function->expects(static::atLeastOnce())
            ->method('getCompilationUnit')
            ->willReturn($this->getPHPDependFileMock($fileName));

        return $function;
    }

    /**
     * Creates a mocked PHP_Depend method instance.
     *
     * @param ?string $fileName Optional file name for the source file.
     */
    protected function getPHPDependMethodMock(?string $fileName = '/foo/bar.php'): ASTMethod&MockObject
    {
        $method = $this->getMockBuilder(ASTMethod::class)
            ->setConstructorArgs([null])
            ->getMock();
        $method->expects(static::atLeastOnce())
            ->method('getCompilationUnit')
            ->willReturn($this->getPHPDependFileMock($fileName));

        return $method;
    }

    /**
     * Creates a mocked PHP_Depend file instance.
     *
     * @param ?string $fileName The temporary file name.
     */
    protected function getPHPDependFileMock(?string $fileName): ASTCompilationUnit&MockObject
    {
        $file = $this->getMockBuilder(ASTCompilationUnit::class)
            ->setConstructorArgs([null])
            ->getMock();
        $file->expects(static::any())
            ->method('getFileName')
            ->willReturn($fileName);

        return $file;
    }
}
