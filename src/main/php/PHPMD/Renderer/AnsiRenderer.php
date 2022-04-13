<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;
use PHPMD\RuleViolation;

/**
 * This renderer output a command line friendly log with all found violations
 * and suspect software artifacts.
 */
class AnsiRenderer extends AbstractRenderer
{

    /**
     * @param \PHPMD\Report $report
     * @return void
     */
    public function renderReport(Report $report)
    {
        $this->writeViolationsReport($report);
        $this->writeErrorsReport($report);
        $this->writeReportSummary($report);
    }

    /**
     * @param \PHPMD\Report $report
     * @return void
     */
    private function writeViolationsReport(Report $report)
    {
        if ($report->isEmpty()) {
            return;
        }

        $padding = $this->getMaxLineNumberLength($report);
        $previousFile = null;
        foreach ($report->getRuleViolations() as $violation) {
            if ($violation->getFileName() !== $previousFile) {
                if ($previousFile !== null) {
                    $this->getWriter()->write(PHP_EOL);
                }

                $this->writeViolationFileHeader($violation);
            }

            $this->writeViolationLine($violation, $padding);
            $previousFile = $violation->getFileName();
        }
    }

    /**
     * @param \PHPMD\Report $report
     * @return int|null
     */
    private function getMaxLineNumberLength(Report $report)
    {
        $maxLength = null;
        foreach ($report->getRuleViolations() as $violation) {
            if ($maxLength === null || strlen($violation->getBeginLine()) > $maxLength) {
                $maxLength = strlen($violation->getBeginLine());
            }
        }

        return $maxLength;
    }

    /**
     * @param \PHPMD\RuleViolation $violation
     * @return void
     */
    private function writeViolationFileHeader(RuleViolation $violation)
    {
        $fileHeader = sprintf(
            'FILE: %s',
            $violation->getFileName()
        );
        $this->getWriter()->write(
            PHP_EOL . $fileHeader . PHP_EOL .
            str_repeat('-', strlen($fileHeader)) . PHP_EOL
        );
    }

    /**
     * @param \PHPMD\RuleViolation $violation
     * @param int $padding
     * @return void
     */
    private function writeViolationLine(RuleViolation $violation, $padding)
    {
        $this->getWriter()->write(sprintf(
            " %s | \e[31mVIOLATION\e[0m | %s" . PHP_EOL,
            str_pad($violation->getBeginLine(), $padding, ' '),
            $violation->getDescription()
        ));
    }

    /**
     * @param \PHPMD\Report $report
     * @return void
     */
    private function writeErrorsReport(Report $report)
    {
        if (!$report->hasErrors()) {
            return;
        }

        /** @var ProcessingError $error */
        foreach ($report->getErrors() as $error) {
            $errorHeader = sprintf(
                "\e[33mERROR\e[0m while parsing %s",
                $error->getFile()
            );

            $this->getWriter()->write(
                PHP_EOL . $errorHeader . PHP_EOL .
                str_repeat('-', strlen($errorHeader) - 9) . PHP_EOL
            );

            $this->getWriter()->write(sprintf(
                '%s' . PHP_EOL,
                $error->getMessage()
            ));
        }
    }

    /**
     * @param \PHPMD\Report $report
     * @return void
     */
    private function writeReportSummary(Report $report)
    {
        $this->getWriter()->write(
            sprintf(
                PHP_EOL . 'Found %s %s and %s %s in %sms' . PHP_EOL,
                count($report->getRuleViolations()),
                count($report->getRuleViolations()) !== 1 ? 'violations' : 'violation',
                iterator_count($report->getErrors()),
                iterator_count($report->getErrors()) !== 1 ? 'errors' : 'error',
                $report->getElapsedTimeInMillis()
            )
        );
        if (count($report->getRuleViolations()) === 0 && iterator_count($report->getErrors()) === 0) {
            $this->getWriter()->write(PHP_EOL . "\e[32mNo mess detected\e[0m" . PHP_EOL);
        }
    }
}
