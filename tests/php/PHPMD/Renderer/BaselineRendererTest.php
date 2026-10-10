<?php

namespace PHPMD\Renderer;

use ArrayIterator;
use PHPMD\AbstractTestCase;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Baseline\BaselineSet;
use PHPMD\Baseline\BaselineValidator;
use PHPMD\Baseline\ViolationBaseline;
use PHPMD\Report;
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
    public function testRenderReportInUpdateModeWritesOnlyBaselinedViolations(): void
    {
        $writer = new BufferedOutput();
        $baselined = [
            $this->getRuleViolationMock('/src/php/bar.php'),
            $this->getRuleViolationMock('/src/php/foo.php'),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::never())->method('getRuleViolations');
        $report->expects(static::once())
            ->method('getBaselinedRuleViolations')
            ->willReturn(new ArrayIterator($baselined));

        $renderer = new BaselineRenderer('/src', BaselineMode::Update);
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
    public function testUpdateOrderMatchesGenerateOrderRegardlessOfFileArrival(): void
    {
        $a10 = $this->getRuleViolationMock('/src/a.php', 10, 10);
        $a10->method('getMethodName')->willReturn('later');
        $a2 = $this->getRuleViolationMock('/src/a.php', 2, 2);
        $a2->method('getMethodName')->willReturn('earlier');
        $b1 = $this->getRuleViolationMock('/src/b.php', 1, 1);
        $b1->method('getMethodName')->willReturn('first');
        $baseline = new BaselineSet();
        foreach ([$a2, $a10, $b1] as $violation) {
            $baseline->addEntry(new ViolationBaseline(
                $violation->getRule()::class,
                (string) $violation->getFileName(),
                $violation->getMethodName()
            ));
        }
        $xml = [];
        foreach ([[$b1, $a10, $a2, $a10], [$a2, $a10, $b1, $a10]] as $order) {
            foreach ([BaselineMode::Generate, BaselineMode::Update] as $mode) {
                $report = new Report($mode === BaselineMode::Update ? new BaselineValidator($baseline) : null);
                foreach ($order as $violation) {
                    $report->addRuleViolation($violation);
                }
                $writer = new BufferedOutput();
                $renderer = new BaselineRenderer('/src', $mode);
                $renderer->setWriter($writer);
                $renderer->start();
                $renderer->renderReport($report);
                $renderer->end();
                $xml[] = $writer->fetch();
            }
        }

        foreach (array_slice($xml, 1) as $actual) {
            static::assertSame($xml[0], $actual);
        }
        static::assertSame(3, substr_count($xml[0], '<violation '));
        static::assertLessThan(strpos($xml[0], 'method="later"'), strpos($xml[0], 'method="earlier"'));
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
