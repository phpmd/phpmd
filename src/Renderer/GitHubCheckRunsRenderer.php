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

namespace PHPMD\Renderer;

use JsonException;
use PHPMD\AbstractRenderer;
use PHPMD\Report;
use PHPMD\TextUI\Command;

/**
 * This class will render a report for GitHub Check Runs.
 */
final class GitHubCheckRunsRenderer extends AbstractRenderer
{
    public function renderReport(Report $report): void
    {
        $data = $this->initReportData($report);
        $data = $this->addViolationsToReport($report, $data);
        $jsonData = $this->encodeReport($data);

        $writer = $this->getWriter();
        $writer->write($jsonData . PHP_EOL);
    }

    /**
     * Create report data and add renderer meta properties
     *
     * @return array<string, string>
     */
    private function initReportData(Report $report): array
    {
        return [
            'title' => sprintf('%s %s', 'phpmd', Command::getVersion()),
            'summary' => $this->getReportSummary($report),
        ];
    }

    /**
     * Add violations, if any, to the report data
     *
     * @param Report $report The report with potential violations.
     * @param array<string, mixed> $data The report output to add the violations to.
     * @return array<string, mixed> The report output with violations, if any.
     */
    private function addViolationsToReport(Report $report, array $data): array
    {
        $filesList = [];
        foreach ($report->getRuleViolations() as $violation) {
            $fileName = $violation->getFileName();
            $rule = $violation->getRule();
            $filesList[$fileName ?? '']['path'] = $fileName;
            $filesList[$fileName ?? '']['violations'][] = [
                'start_line' => $violation->getBeginLine(),
                'end_line' => $violation->getEndLine(),
                'annotation_level' => $this->getAnnotationLevelFromPriority($rule->getPriority()),
                'message' => $violation->getDescription(),
                'title' => $rule->getName(),
                'raw_details' => [
                    'package' => $violation->getNamespaceName(),
                    'function' => $violation->getFunctionName(),
                    'class' => $violation->getClassName(),
                    'method' => $violation->getMethodName(),
                    'rule' => $rule->getName(),
                    'ruleSet' => $rule->getRuleSetName(),
                    'externalInfoUrl' => $rule->getExternalInfoUrl(),
                    'priority' => $rule->getPriority(),
                ],
            ];
        }
        $data['annotations'] = array_values($filesList);

        return $data;
    }

    private function getAnnotationLevelFromPriority(int $priority): string
    {
        $levels = [
            1 => 'failure',
            2 => 'warning',
            3 => 'warning',
            4 => 'notice',
            5 => 'notice',
        ];

        return $levels[$priority];
    }

    /**
     * Encode report data to the JSON representation string
     *
     * @param array<mixed> $data The report data
     * @throws JsonException
     */
    private function encodeReport(array $data): string
    {
        $encodeOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
            | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR;

        return json_encode($data, $encodeOptions);
    }

    private function getReportSummary(Report $report): string
    {
        if (count($report->getRuleViolations()) === 0 && iterator_count($report->getErrors()) === 0) {
            return 'No mess detected';
        }

        return sprintf(
            PHP_EOL . 'Found %s %s and %s %s in %sms',
            count($report->getRuleViolations()),
            count($report->getRuleViolations()) !== 1 ? 'violations' : 'violation',
            iterator_count($report->getErrors()),
            iterator_count($report->getErrors()) !== 1 ? 'errors' : 'error',
            $report->getElapsedTimeInMillis()
        );
    }
}
