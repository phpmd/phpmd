<?php
/**
 * Initial version created on: 03.12.2020 14:31
 *
 * This renderer uses parts of the HTMLRenderer by Premysl Karbula
 *
 * @author  Tebin Ulrich <info@tebinulrich.de>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Renderer;

use PHPMD\Renderer\HTMLRenderer;
use PHPMD\Report;
use SplFileObject;

/**
 * Class DatatablesRenderer
 *
 * @package PHPMD\Renderer
 */
class DatatablesRenderer extends HTMLRenderer
{
    /**
     * Writes the beginning of the report, including css and js
     */
    public function start() {
        $writer = $this->getWriter();
        
        $cssDatatables = file_get_contents(__DIR__ . '/DatatablesRenderer/jquery.dataTables.min.css');
        $cssLocal      = file_get_contents(__DIR__ . '/DatatablesRenderer/DatatablesRenderer.css');
        $jsjQuery      = file_get_contents(__DIR__ . '/DatatablesRenderer/jquery-3.5.1.js');
        $jsDatatables  = file_get_contents(__DIR__ . '/DatatablesRenderer/jquery.dataTables.min.js');
        
        $writer->write(
            "<!DOCTYPE html>
<head>
    <title>PHPMD Datatables Report</title>
    <link rel='shortcut icon' href='data:image/x-icon;,' type='image/x-icon'>
    <style>
    {$cssDatatables}
    {$cssLocal}
    </style>
    <script  type='text/javascript'>
    {$jsjQuery}
    {$jsDatatables}
    </script>
</head>
<body>" . PHP_EOL
        );
    }
    
    /**
     * @param $violation
     *
     * @return string
     */
    public function getPreview($violation) {
        $previewLines = self::getLineExcerpt($violation->getFileName(), $violation->getBeginLine(), 2);
        
        $excerptHtml = '<ul class="code">';
        foreach ($previewLines as $line => $code) {
            $class       = $line === $violation->getBeginLine() ? " class='hlt'" : null;
            $codeHtml    = htmlspecialchars($code);
            $excerptHtml .= "<li{$class}><div class='no'>{$line}</div><div class='cd'>{$codeHtml}</div></li>";
        }
        $excerptHtml .= '</ul>';
        
        return $excerptHtml;
    }
    
    /**
     * Writes the report
     *
     * @param Report $report
     */
    public function renderReport(Report $report) {
        $violations = $report->getRuleViolations();
        $writer     = $this->getWriter();
        
        $writer->write(
            "
            <h2>PHPMD Datatables Report</h2>
            <div class='info'>generated in <b>" . ($report->getElapsedTimeInMillis(
                ) / 1000) . "</b> seconds.<br />A total of <b>" . count($violations) . "</b> violations have been detected.</div>
        "
        );
        
        $writer->write(
            "
        <table id='phpmdDatatable' class='display' style='width:100%'>
        <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Filename</th>
                <th>Violation</th>
                <th>Priority</th>
                <th>Rulename</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>"
        );
        
        $index = 1;
        foreach ($violations as $violation) {
            $preview = $this->getPreview($violation);
            
            $ruleName = $violation->getRule()->getName();
            $ruleUrl  = $violation->getRule()->getExternalInfoUrl();
            
            if ($ruleUrl !== '#') {
                $ruleName = "<a target='_blank' href='" . $ruleUrl . "'>" . $ruleName . "</a>";
            }
            
            $writer->write(
                "
                <tr>
                    <td></td>
                    <td>#" . $index . "</td>
                    <td>" . $violation->getFileName() . "</td>
                    <td>" . $violation->getDescription() . "</td>
                    <td>" . self::$priorityTitles[$violation->getRule()->getPriority()] . "</td>
                    <td>" . $ruleName . "</td>
                    <td>" . $preview . "</td>
                </tr>
                "
            );
            
            $index = $index + 1;
        }
        
        $writer->write(
            "
        </tbody>
    </table>" . $this->getDt() . PHP_EOL
        );
    }
    
    /**
     * Writes the end of the report
     */
    public function end() {
        $writer = $this->getWriter();
        $writer->write("</body>" . PHP_EOL . "</html>");
    }
    
    protected function getDt() {
        return "<script>
                    $(document).ready(function() {
                        
                        function format ( d ) {
                          return d[6];
                        }
                        
                        var table = $('#phpmdDatatable').DataTable({
                            columnDefs: [
                              {
                                targets: 0,
                                className:      'details-control',
                                orderable:      false,
                                data:           null,
                                defaultContent: ''
                              },
                              {
                                targets: [6],
                                visible: false
                              }
                            ],
                            pageLength: 100
                        });
                        
                        $('#phpmdDatatable tbody').on('click', 'td.details-control', function () {
                            var tr = $(this).closest('tr');
                            var row = table.row( tr );
                     
                            if ( row.child.isShown() ) {
                                // This row is already open - close it
                                row.child.hide();
                                tr.removeClass('shown');
                            }
                            else {
                                // Open this row
                                row.child( format(row.data()) ).show();
                                tr.addClass('shown');
                            }
                        } );
                        
                        
                    } );
                    </script>";
    }
    
    
}