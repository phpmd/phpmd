<?php

namespace PHPMD\Renderer;

use ArrayIterator;
use PHPMD\AbstractTest;
use PHPMD\Report;
use PHPMD\Stubs\WriterStub;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @coversDefaultClass \PHPMD\Renderer\BaselineRenderer
 * @covers ::__construct
 */
class BaselineRendererTest extends AbstractTest
{
    /**
     * @covers ::renderReport
     */
    public function testRenderReport()
    {
        $writer     = new WriterStub();
        $violations = array(
            $this->getRuleViolationMock('/src/php/bar.php'),
            $this->getRuleViolationMock('/src/php/foo.php'),
        );

        /** @var Report|MockObject $report */
        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator($violations));

        $renderer = new BaselineRenderer('/src');
        $renderer->setWriter($writer);
        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertXmlEquals(
            $writer->getData(),
            'renderer/baseline_renderer_expected1.xml'
        );
    }

    /**
     * @covers ::renderReport
     */
    public function testRenderReportShouldWriteMethodName()
    {
        $writer        = new WriterStub();
        $violationMock = $this->getRuleViolationMock('/src/php/bar.php');
        $violationMock->expects(static::once())->method('getMethodName')->willReturn('foo');

        /** @var Report|MockObject $report */
        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator(array($violationMock)));

        $renderer = new BaselineRenderer('/src');
        $renderer->setWriter($writer);
        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertXmlEquals(
            $writer->getData(),
            'renderer/baseline_renderer_expected2.xml'
        );
    }

    /**
     * @covers ::renderReport
     */
    public function testRenderReportShouldDeduplicateSimilarViolations()
    {
        $writer        = new WriterStub();
        $violationMock = $this->getRuleViolationMock('/src/php/bar.php');
        $violationMock->expects(static::exactly(2))->method('getMethodName')->willReturn('foo');

        // add the same violation twice
        /** @var Report|MockObject $report */
        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator(array($violationMock, $violationMock)));

        $renderer = new BaselineRenderer('/src');
        $renderer->setWriter($writer);
        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertXmlEquals(
            $writer->getData(),
            'renderer/baseline_renderer_expected2.xml'
        );
    }

    /**
     * @covers ::renderReport
     */
    public function testRenderEmptyReport()
    {
        $writer = new WriterStub();
        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator(array()));

        /** @var Report|MockObject $report */
        $renderer = new BaselineRenderer('/src');
        $renderer->setWriter($writer);
        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertXmlEquals(
            $writer->getData(),
            'renderer/baseline_renderer_expected3.xml'
        );
    }
}
