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
use PHPMD\RuleViolation;

/**
 * This class will render a JSON report.
 */
class JSONRenderer extends AbstractRenderer
{
    /**
     * Create report data and add renderer meta properties
     *
     * @return array
     */
    private function initReportData()
    {
        $data = array(
            'version' => PHPMD::VERSION,
            'package' => 'phpmd',
            'timestamp' => date('c'),
        );

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function renderReport(Report $report)
    {
        $filesList = array();
        /** @var RuleViolation $violation */
        foreach ($report->getRuleViolations() as $violation) {
            $fileName = $violation->getFileName();
            $rule = $violation->getRule();
            $filesList[$fileName]['file'] = $fileName;
            $filesList[$fileName]['violations'][] = array(
                'beginLine' => $violation->getBeginLine(),
                'endLine' => $violation->getEndLine(),
                'package' => $violation->getNamespaceName(),
                'function' => $violation->getFunctionName(),
                'class' => $violation->getClassName(),
                'method' => $violation->getMethodName(),
                'description' => $violation->getDescription(),
                'rule' => $rule->getName(),
                'ruleSet' => $rule->getRuleSetName(),
                'externalInfoUrl' => $rule->getExternalInfoUrl(),
                'priority' => $rule->getPriority(),
            );
        }

        $errorsList = array();
        foreach ($report->getErrors() as $error) {
            $errorsList[] = array(
                'fileName' => $error->getFile(),
                'message' => $error->getMessage(),
            );
        }
        
        $data = $this->initReportData();
        $data['files'] = array_values($filesList);
        if (count($errorsList)) {
            $data['errors'] = $errorsList;
        }

        $writer = $this->getWriter();
        $json = $this->encodeReport($data);
        $writer->write($json . PHP_EOL);
    }

    /**
     * Encode report data to the JSON representation string
     *
     * @param $data array The report data
     *
     * @return string
     */
    private function encodeReport($data)
    {
        $encodeOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
        // JSON_PRETTY_PRINT Available since PHP 5.4.0.
        if (defined('JSON_PRETTY_PRINT')) {
            $encodeOptions |= JSON_PRETTY_PRINT;
        }

        return json_encode($data, $encodeOptions);
    }
}
