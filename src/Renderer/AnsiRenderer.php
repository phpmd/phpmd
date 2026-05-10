<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;
use PHPMD\RuleViolation;

/**
 * This renderer output a command line friendly log with all found violations
 * and suspect software artifacts.
 */
final class AnsiRenderer extends AbstractRenderer
{
    public function renderReport(Report $report): void
    {
        $this->writeViolationsReport($report);
        $this->writeErrorsReport($report);
        $this->writeReportSummary($report);
    }

    private function writeViolationsReport(Report $report): void
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

            $this->writeViolationLine($violation, (int) $padding);
            $previousFile = $violation->getFileName();
        }
    }

    private function getMaxLineNumberLength(Report $report): ?int
    {
        $maxLength = null;
        foreach ($report->getRuleViolations() as $violation) {
            if ($maxLength === null || strlen((string) $violation->getBeginLine()) > $maxLength) {
                $maxLength = strlen((string) $violation->getBeginLine());
            }
        }

        return $maxLength;
    }

    private function writeViolationFileHeader(RuleViolation $violation): void
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

    private function writeViolationLine(RuleViolation $violation, int $padding): void
    {
        $this->getWriter()->write(sprintf(
            " %s | \e[31mVIOLATION\e[0m | %s" . PHP_EOL,
            str_pad((string) $violation->getBeginLine(), $padding, ' '),
            $violation->getDescription()
        ));
    }

    private function writeErrorsReport(Report $report): void
    {
        if (!$report->hasErrors()) {
            return;
        }

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

    private function writeReportSummary(Report $report): void
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
