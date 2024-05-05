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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;
use PHPMD\RuleViolation;

/**
 * This class will render a GitLab compatible JSON report.
 */
class GitLabRenderer extends AbstractRenderer
{
    /**
     * {@inheritDoc}
     */
    public function renderReport(Report $report): void
    {
        $data = $this->addViolationsToReport($report);
        $data = $this->addErrorsToReport($report, $data);
        $jsonData = $this->encodeReport($data);

        $writer = $this->getWriter();
        $writer->write($jsonData . PHP_EOL);
    }


    /**
     * Add violations, if any, to GitLab Code Quality report format
     *
     * @param Report $report The report with potential violations.
     *
     * @return array The report output with violations, if any.
     */
    protected function addViolationsToReport(Report $report)
    {
        $data = [];

        /** @var RuleViolation $violation */
        foreach ($report->getRuleViolations() as $violation) {
            $violationResult = [
                'type' => 'issue',
                'categories' =>
                    [
                        'Style',
                        'PHP',
                    ],
                'check_name' => $violation->getRule()->getName(),
                'fingerprint' => $violation->getFileName()
                    . ':' . $violation->getBeginLine()
                    . ':' . $violation->getRule()->getName(),
                'description' => $violation->getDescription(),
                'severity' => 'minor',
                'location' =>
                    [
                        'path' => $violation->getFileName(),
                        'lines' =>
                            [
                                'begin' => $violation->getBeginLine(),
                                'end' => $violation->getEndLine(),
                            ],
                    ],
            ];

            $data[] = $violationResult;
        }

        return $data;
    }

    /**
     * Add errors, if any, to GitLab Code Quality report format
     *
     * @param Report $report The report with potential errors.
     * @param array  $data   The report output to add the errors to.
     *
     * @return array The report output with errors, if any.
     */
    protected function addErrorsToReport(Report $report, array $data)
    {
        $errors = $report->getErrors();
        foreach ($errors as $error) {
            $errorResult = [
                'description' => $error->getMessage(),
                'fingerprint' => $error->getFile() . ':0:MajorErrorInFile',
                'severity' => 'major',
                'location' =>
                    [
                        'path' => $error->getFile(),
                        'lines' =>
                            [
                                'begin' => 0,
                            ],
                    ],
            ];

            $data[] = $errorResult;
        }

        return $data;
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
}
