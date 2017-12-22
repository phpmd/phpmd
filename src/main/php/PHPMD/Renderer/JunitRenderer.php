<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Jelle van der Waa <jelle@vdwaa.nl>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Jelle van der Waa <jelle@vdwaa.nl>
 * @copyright Jelle van der Waa. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;

/**
 * This class will render a Junit compatible xml-report for Jenkins integration.
 */
class JunitRenderer extends AbstractRenderer
{
    /**
     * Temporary property that holds the name of the last rendered file, it is
     * used to detect the next processed file.
     *
     * @var string
     */
    private $fileName = null;

    /**
     * This method will be called on all renderers before the engine starts the
     * real report processing.
     *
     * @return void
     */
    public function start()
    {
        $writer = $this->getWriter();

        $this->getWriter()->write('<?xml version="1.0" encoding="UTF-8" ?>');
        $writer->write(PHP_EOL);
    }

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     *
     * @param \PHPMD\Report $report
     * @return void
     */
    public function renderReport(Report $report)
    {
        $writer = $this->getWriter();
        $writer->write('<testsuites>' . PHP_EOL);

        $data = [];
        foreach ($report->getRuleViolations() as $violation) {
            $fileName = $violation->getFileName();

            if ($this->fileName !== $fileName) {
                // Not first file
                if ($this->fileName !== null) {
                    $data[$fileName] = [];
                }
                // Store current file name
                $this->fileName = $fileName;
            }

            $data[$fileName][] = [
                'name' => $violation->getRule()->getName(),
                'desc' => $violation->getDescription(),
                'lineno' => $violation->getBeginLine(),
                'ruleset' => $violation->getRule()->getRuleSetName(),
            ];
        }

        foreach ($report->getErrors() as $error) {
            $fileName = $error->getFile();
            $data[$fileName][] = [
                'name' => 'error',
                'desc' => htmlspecialchars($error->getMessage())
            ];
        }

        foreach ($data as $fileName => $entrys) {
            $writer->write('  <testsuite package="PHPMD" name="' . $fileName . '"');
            $writer->write(' time="0"');
            $writer->write(' tests="' . count($entrys) . '"');
            $writer->write(' errors="' . count($entrys) . '"');
            $writer->write('>' . PHP_EOL);

            foreach ($entrys as $entry) {
                $writer->write('    <testcase time="0"');
                $writer->write(' name="' . $entry['name'] . '">');
                $writer->write('<failure message="' . $entry['desc'] . '">');
                if ($entry['name'] !== 'error') {
                    $writer->write('<![CDATA[line ' . $entry['lineno']);
                    $writer->write(', Error - ' . $entry['desc']);
                    $writer->write(' (' . $entry['ruleset']  .')]]>');
                }
                $writer->write('</failure>');
                $writer->write(PHP_EOL);
                $writer->write('    </testcase>' . PHP_EOL);
            }

            $writer->write('  </testsuite>' . PHP_EOL);
        }

        $writer->write('</testsuites>' . PHP_EOL);
    }
}
