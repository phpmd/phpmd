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

use PHPMD\AbstractRenderer;
use PHPMD\PHPMD;
use PHPMD\Report;

/**
 * This class will render a report for GitHub Check Runs.
 */
class GitHubCheckRunsRenderer extends AbstractRenderer
{
    /**
     * {@inheritDoc}
     */
    public function renderReport(Report $report)
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
     * @param Report $report The report with potential violations.
     * @return array
     */
    protected function initReportData(Report $report)
    {
        $data = array(
            'title' => sprintf('%s %s', 'phpmd', PHPMD::VERSION),
            'summary' => $this->getReportSummary($report),
        );

        return $data;
    }

    /**
     * Add violations, if any, to the report data
     *
     * @param Report $report The report with potential violations.
     * @param array $data The report output to add the violations to.
     * @return array The report output with violations, if any.
     */
    protected function addViolationsToReport(Report $report, array $data)
    {
        $filesList = array();
        /** @var RuleViolation $violation */
        foreach ($report->getRuleViolations() as $violation) {
            $fileName = $violation->getFileName();
            $rule = $violation->getRule();
            $filesList[$fileName]['path'] = $fileName;
            $filesList[$fileName]['violations'][] = array(
                'start_line' => $violation->getBeginLine(),
                'end_line' => $violation->getEndLine(),
                'annotation_level' => $this->getAnnotationLevelFromPriority($rule->getPriority()),
                'message' => $violation->getDescription(),
                'title' => $rule->getName(),
                'raw_details' => array(
                    'package' => $violation->getNamespaceName(),
                    'function' => $violation->getFunctionName(),
                    'class' => $violation->getClassName(),
                    'method' => $violation->getMethodName(),
                    'rule' => $rule->getName(),
                    'ruleSet' => $rule->getRuleSetName(),
                    'externalInfoUrl' => $rule->getExternalInfoUrl(),
                    'priority' => $rule->getPriority(),
                ),
            );
        }
        $data['annotations'] = array_values($filesList);

        return $data;
    }

    private function getAnnotationLevelFromPriority(int $priority): string
    {
        $levels = array(
            1 => 'failure',
            2 => 'warning',
            3 => 'warning',
            4 => 'notice',
            5 => 'notice',
        );

        return $levels[$priority];
    }

    /**
     * Encode report data to the JSON representation string
     *
     * @param array $data The report data
     *
     * @return string
     */
    private function encodeReport($data)
    {
        $encodeOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP |
            (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0);

        return json_encode($data, $encodeOptions);
    }

    /**
     * @param \PHPMD\Report $report
     * @return string
     */
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
