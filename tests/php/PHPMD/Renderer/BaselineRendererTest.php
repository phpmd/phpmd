<?php

namespace PHPMD\Renderer;

use ArrayIterator;
use PHPMD\AbstractTestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @coversDefaultClass \PHPMD\Renderer\BaselineRenderer
 * @covers ::__construct
 */
class BaselineRendererTest extends AbstractTestCase
{
    /**
     * @covers ::renderReport
     */
    public function testRenderReport(): void
    {
        $writer = new BufferedOutput();
        $violations = [
            $this->getRuleViolationMock('/src/php/bar.php'),
            $this->getRuleViolationMock('/src/php/foo.php'),
        ];

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
            $writer->fetch(),
            'renderer/baseline_renderer_expected1.xml'
        );
    }

    /**
     * @covers ::renderReport
     */
    public function testRenderReportShouldWriteMethodName(): void
    {
        $writer = new BufferedOutput();
        $violationMock = $this->getRuleViolationMock('/src/php/bar.php');
        $violationMock->expects(static::once())->method('getMethodName')->willReturn('foo');

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator([$violationMock]));

        $renderer = new BaselineRenderer('/src');
        $renderer->setWriter($writer);
        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertXmlEquals(
            $writer->fetch(),
            'renderer/baseline_renderer_expected2.xml'
        );
    }

    /**
     * @covers ::renderReport
     */
    public function testRenderReportShouldDeduplicateSimilarViolations(): void
    {
        $writer = new BufferedOutput();
        $violationMock = $this->getRuleViolationMock('/src/php/bar.php');
        $violationMock->expects(static::exactly(2))->method('getMethodName')->willReturn('foo');

        // add the same violation twice
        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator([$violationMock, $violationMock]));

        $renderer = new BaselineRenderer('/src');
        $renderer->setWriter($writer);
        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertXmlEquals(
            $writer->fetch(),
            'renderer/baseline_renderer_expected2.xml'
        );
    }

    /**
     * @covers ::renderReport
     */
    public function testRenderEmptyReport(): void
    {
        $writer = new BufferedOutput();
        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator([]));

        $renderer = new BaselineRenderer('/src');
        $renderer->setWriter($writer);
        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertXmlEquals(
            $writer->fetch(),
            'renderer/baseline_renderer_expected3.xml'
        );
    }
}
