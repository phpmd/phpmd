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
use PHPMD\Report;

/**
 * This renderer output a simple html file with all found violations and suspect
 * software artifacts.
 */
class HTMLRenderer extends AbstractRenderer
{
    /**
     * This method will be called on all renderers before the engine starts the
     * real report processing.
     *
     * @return void
     */
    public function start()
    {
        $writer = $this->getWriter();

        $writer->write('<html><head><title>PHPMD</title></head><body>');
        $writer->write(PHP_EOL);
        $writer->write('<center><h1>PHPMD report</h1></center>');
        $writer->write('<center><h2>Problems found</h2></center>');
        $writer->write(PHP_EOL);
        $writer->write('<table align="center" cellspacing="0" cellpadding="3">');
        $writer->write('<tr>');
        $writer->write('<th>#</th><th>File</th><th>Line</th><th>Problem</th>');
        $writer->write('</tr>');
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
        $index = 0;

        $writer = $this->getWriter();
        foreach ($report->getRuleViolations() as $violation) {
            $writer->write('<tr');
            if (++$index % 2 === 1) {
                $writer->write(' bgcolor="lightgrey"');
            }
            $writer->write('>');
            $writer->write(PHP_EOL);

            $writer->write('<td align="center">');
            $writer->write($index);
            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('<td>');
            $writer->write(htmlentities($violation->getFileName()));
            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('<td align="center" width="5%">');
            $writer->write($violation->getBeginLine());
            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('<td>');
            if ($violation->getRule()->getExternalInfoUrl()) {
                $writer->write('<a href="');
                $writer->write($violation->getRule()->getExternalInfoUrl());
                $writer->write('">');
            }

            $writer->write(htmlentities($violation->getDescription()));
            if ($violation->getRule()->getExternalInfoUrl()) {
                $writer->write('</a>');
            }

            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('</tr>');
            $writer->write(PHP_EOL);
        }

        $writer->write('</table>');

        $this->glomProcessingErrors($report);
    }

    /**
     * This method will be called the engine has finished the report processing
     * for all registered renderers.
     *
     * @return void
     */
    public function end()
    {
        $writer = $this->getWriter();
        $writer->write('</body></html>');
    }

    /**
     * This method will render a html table with occurred processing errors.
     *
     * @param \PHPMD\Report $report
     * @return void
     * @since 1.2.1
     */
    private function glomProcessingErrors(Report $report)
    {
        if (false === $report->hasErrors()) {
            return;
        }

        $writer = $this->getWriter();

        $writer->write('<hr />');
        $writer->write('<center><h3>Processing errors</h3></center>');
        $writer->write('<table align="center" cellspacing="0" cellpadding="3">');
        $writer->write('<tr><th>File</th><th>Problem</th></tr>');

        $index = 0;
        foreach ($report->getErrors() as $error) {
            $writer->write('<tr');
            if (++$index % 2 === 1) {
                $writer->write(' bgcolor="lightgrey"');
            }
            $writer->write('>');
            $writer->write('<td>' . $error->getFile() . '</td>');
            $writer->write('<td>' . htmlentities($error->getMessage()) . '</td>');
            $writer->write('</tr>' . PHP_EOL);
        }

        $writer->write('</table>');
    }
}
