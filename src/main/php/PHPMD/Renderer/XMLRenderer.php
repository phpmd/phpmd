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
 * This class will render a Java-PMD compatible xml-report.
 */
class XMLRenderer extends AbstractRenderer
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
        $this->getWriter()->write('<?xml version="1.0" encoding="UTF-8" ?>');
        $this->getWriter()->write(PHP_EOL);
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
        $writer->write('<pmd version="' . PHPMD::VERSION . '" ');
        $writer->write('timestamp="' . date('c') . '">');
        $writer->write(PHP_EOL);

        foreach ($report->getRuleViolations() as $violation) {
            $fileName = $violation->getFileName();

            if ($this->fileName !== $fileName) {
                // Not first file
                if ($this->fileName !== null) {
                    $writer->write('  </file>' . PHP_EOL);
                }
                // Store current file name
                $this->fileName = $fileName;

                $writer->write('  <file name="' . $fileName . '">' . PHP_EOL);
            }

            $rule = $violation->getRule();

            $writer->write('    <violation');
            $writer->write(' beginline="' . $violation->getBeginLine() . '"');
            $writer->write(' endline="' . $violation->getEndLine() . '"');
            $writer->write(' rule="' . $rule->getName() . '"');
            $writer->write(' ruleset="' . $rule->getRuleSetName() . '"');

            $this->maybeAdd('package', $violation->getNamespaceName());
            $this->maybeAdd('externalInfoUrl', $rule->getExternalInfoUrl());
            $this->maybeAdd('function', $violation->getFunctionName());
            $this->maybeAdd('class', $violation->getClassName());
            $this->maybeAdd('method', $violation->getMethodName());
            //$this->_maybeAdd('variable', $violation->getVariableName());

            $writer->write(' priority="' . $rule->getPriority() . '"');
            $writer->write('>' . PHP_EOL);
            $writer->write('      ' . htmlspecialchars($violation->getDescription()) . PHP_EOL);
            $writer->write('    </violation>' . PHP_EOL);
        }

        // Last file and at least one violation
        if ($this->fileName !== null) {
            $writer->write('  </file>' . PHP_EOL);
        }

        foreach ($report->getErrors() as $error) {
            $writer->write('  <error filename="');
            $writer->write($error->getFile());
            $writer->write('" msg="');
            $writer->write(htmlspecialchars($error->getMessage()));
            $writer->write('" />' . PHP_EOL);
        }

        $writer->write('</pmd>' . PHP_EOL);
    }

    /**
     * This method will write a xml attribute named <b>$attr</b> to the output
     * when the given <b>$value</b> is not an empty string and is not <b>null</b>.
     *
     * @param string $attr  The xml attribute name.
     * @param string $value The attribute value.
     * @return void
     */
    private function maybeAdd($attr, $value)
    {
        if ($value === null || trim($value) === '') {
            return;
        }
        $this->getWriter()->write(' ' . $attr . '="' . $value . '"');
    }
}
