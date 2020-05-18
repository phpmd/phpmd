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
 * This renderer output a html file with all found violations.
 *
 * @author Premysl Karbula <premavansmuuf@gmail.com>
 * @copyright 2017 Premysl Karbula. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class HTMLRenderer extends AbstractRenderer
{
    const CATEGORY_PRIORITY = 'category_priority';

    const CATEGORY_NAMESPACE = 'category_namespace';

    const CATEGORY_RULESET = 'category_ruleset';

    const CATEGORY_RULE = 'category_rule';

    protected static $priorityTitles = array(
        1 => 'Top (1)',
        2 => 'High (2)',
        3 => 'Moderate (3)',
        4 => 'Low (4)',
        5 => 'Lowest (5)',
    );

    // Used in self::colorize() method.
    protected static $descHighlightRules = array(
        'method' => array( // Method names.
            'regex' => 'method\s+(((["\']).*["\'])|(\S+))',
            'css-class' => 'hlt-method',
        ),
        'quoted' => array( // Quoted strings.
            'regex' => '(["\'][^\'"]+["\'])',
            'css-class' => 'hlt-quoted',
        ),
        'variable' => array( // Variables.
            'regex' => '(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)',
            'css-class' => 'hlt-variable',
        ),
    );

    protected static $compiledHighlightRegex = null;

    /**
     * This method will be called on all renderers before the engine starts the
     * real report processing.
     *
     * @return void
     */
    public function start()
    {
        $writer = $this->getWriter();

        $mainColor = "#2f838a";

        // Avoid inlining styles.
        $style = "
            <script>
                function toggle(id) {
                    var item = document.getElementById(id);
                    item.classList.toggle('hidden');
                }
            </script>
            <style>

                @media (min-width: 1366px) {
                    body { max-width: 80%; margin: auto; }
                }

                body {
                    font-family: sans-serif;
                }

                a {
                    color: $mainColor;
                }

                a:hover {
                    color: #333;
                }

                em {
                    font-weight: bold;
                    font-style: italic;
                }

                h1 {
                    padding: 0.5ex 0.2ex;
                    border-bottom: 2px solid #333;
                }

                table {
                    width: 100%;
                    border-spacing: 0;
                }

                table tr > th {
                    text-align: left;
                }

                table caption {
                    font-weight: bold;
                    padding: 1ex 0.5ex;
                    text-align: left;
                    font-size: 120%;
                    border-bottom: 2px solid #333;
                }

                tbody tr:nth-child(odd) {
                    background: rgba(0, 0, 0, 0.08);
                }

                tbody tr:hover {
                    background: #ffee99;
                }

                thead th {
                    border-bottom: 1px solid #aaa;
                }

                table td, table th {
                    padding: 0.5ex;
                }

                /* Table 'count' and 'percentage' column */
                .t-cnt, .t-pct {
                    width: 5em;
                }

                .t-pct {
                    opacity: 0.8;
                    font-style: italic;
                    font-size: 80%;
                }

                /* Table bar chart */
                .t-bar {
                    height: 0.5ex;
                    margin-top: 0.5ex;
                    background-color: $mainColor; /* rgba(47, 131, 138, 0.2); */
                }

                section, table {
                    margin-bottom: 2em;
                }

                #details-link.hidden {
                    display: none;
                }

                #details-wrapper.hidden {
                    display: none;
                }

                ul.code {
                    margin: 0;
                    padding: 0;
                }

                ul.code.hidden {
                    display: none;
                }

                ul.code li {
                    display: flex;
                    line-height: 1.4em;
                    font-family: monospace;
                    white-space: nowrap;
                }

                ul.code li:nth-child(odd) {
                    background-color: rgba(47, 131, 138, 0.1)
                }

                /* Excerpt: Line number */
                ul.code .no {
                    width: 5%;
                    min-width: 5em;
                    text-align: right;
                    border-right: 1px solid rgba(47, 131, 138, 0.6);
                    padding-right: 1ex;
                    box-sizing: border-box;
                }

                /* Excerpt: Code */
                ul.code .cd {
                    padding-left: 1ex;
                    white-space: pre-wrap;
                    box-sizing: border-box;
                    word-wrap: break-word;
                    overflow: hidden;
                }

                .hlt {
                    background: #ffee99 !important
                }

                .prio {
                    color: #333;
                    float: right;
                }

                .indx {
                    padding: 0.5ex 1ex;
                    background-color: #000;
                    color: #fff;
                    text-decoration: none;
                }

                .indx:hover {
                    background-color: $mainColor;
                    color: #fff;
                }

                /* Problem container */
                .prb h3 {
                    padding: 1ex 0.5ex;
                    border-bottom: 2px solid #000;
                    font-size: 95%;
                    margin: 0;
                }

                .info-lnk {
                    font-style: italic !important;
                    font-weight: normal !important;
                    text-decoration: none;
                }

                .info-lnk.blck {
                    padding: 0.5ex 1ex;
                    background-color: rgba(47, 131, 138, 0.2);
                }

                .path-basename {
                    font-weight: bold;
                }

                .hlt-info {
                    display: inline-block;
                    padding: 2px 4px;
                    font-style: italic;
                }

                    .hlt-info.quoted {
                        background-color: #92de71;
                    }

                    .hlt-info.variable {
                        background-color: #a3d2ff;
                    }

                    .hlt-info.method {
                        background-color: #f7c0ff;
                    }

                .sub-info {
                    padding: 1ex 0.5ex;
                }

                /* Handle printer friendly styles */
                @media print {
                    body, th { font-size: 10pt; }
                    .hlt-info { padding: 0; background: none; }
                    section, table { margin-bottom: 1em; }
                    h1, h2, h3, table caption { padding: 0.5ex 0.2ex; }
                    .prb h3 { border-bottom: 0.5px solid #aaa; }
                    .t-bar { display: none; }
                    .info-lnk { display: none; }
                    #details-wrapper { display: block !important; font-size: 90% !important; }
                }

            </style>";

        $style = self::reduceWhitespace($style);
        $writer->write("<html><head>{$style}<title>PHPMD Report</title></head><body>" . PHP_EOL);

        $header = sprintf("
            <header>
                <h1>PHPMD Report</h1>
                Generated at <em>%s</em>
                with <a href='%s' target='_blank'>PHP Mess Detector</a>
                on <em>PHP %s</em>
                on <em>%s</em>
            </header>
        ", date('Y-m-d H:i'), "https://phpmd.org", \PHP_VERSION, gethostname());

        $writer->write($header);
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
        $w = $this->getWriter();

        $index = 0;
        $violations = $report->getRuleViolations();

        $count = count($violations);
        $w->write(sprintf('<h3>%d problems found</h3>', $count));

        // If no problems were found, don't bother with rendering anything else.
        if (!$count) {
            return;
        }

        // Render summary tables.
        $w->write("<h2>Summary</h2>");
        $categorized = self::sumUpViolations($violations);
        $this->writeTable('By priority', 'Priority', $categorized[self::CATEGORY_PRIORITY]);
        $this->writeTable('By namespace', 'PHP Namespace', $categorized[self::CATEGORY_NAMESPACE]);
        $this->writeTable('By rule set', 'Rule set', $categorized[self::CATEGORY_RULESET]);
        $this->writeTable('By name', 'Rule name', $categorized[self::CATEGORY_RULE]);

        // Render details of each violation and place the "Details" display toggle.
        $w->write("<h2 style='page-break-before: always'>Details</h2>");
        $w->write("
            <a
                id='details-link'
                class='info-lnk blck'
                href='#'
                onclick='toggle(\"details-link\"); toggle(\"details-wrapper\"); return false;'
            >
            Show details &#x25BC;
        </a>");
        $w->write("<div id='details-wrapper' class='hidden'>");

        foreach ($violations as $violation) {
            // This is going to be used as ID in HTML (deep anchoring).
            $htmlId = "p-" . $index++;

            // Get excerpt of the code from validated file.
            $excerptHtml = null;
            $excerpt = self::getLineExcerpt(
                $violation->getFileName(),
                $violation->getBeginLine(),
                2
            );

            foreach ($excerpt as $line => $code) {
                $class = $line === $violation->getBeginLine() ? " class='hlt'" : null;
                $codeHtml = htmlspecialchars($code);
                $excerptHtml .= "<li{$class}><div class='no'>{$line}</div><div class='cd'>{$codeHtml}</div></li>";
            }

            $descHtml = self::colorize(htmlentities($violation->getDescription()));
            $filePath = $violation->getFileName();
            $fileHtml = "<a href='file://$filePath' target='_blank'>" . self::highlightFile($filePath) . "</a>";

            // Create an external link to rule's help, if there's any provided.
            $linkHtml = null;
            if ($url = $violation->getRule()->getExternalInfoUrl()) {
                $linkHtml = "<a class='info-lnk' href='{$url}' target='_blank'>(help)</a>";
            }

            // HTML snippet handling the toggle to display the file's code.
            $showCodeAnchor = "
                <a class='info-lnk blck' href='#' onclick='toggle(\"$htmlId-code\"); return false;'>
                    Show code &#x25BC;
                </a>";

            $prio = self::$priorityTitles[$violation->getRule()->getPriority()];
            $html = "
                <section class='prb' id='$htmlId'>
                    <header>
                        <h3>
                            <a href='#$htmlId' class='indx'>#{$index}</a>
                            {$descHtml} {$linkHtml} <span class='prio'>{$prio}</span>
                        </h3>
                    </header>
                    <div class='sub-info'><b>File:</b> {$fileHtml} {$showCodeAnchor}</div>
                    <ul class='code hidden' id='$htmlId-code'>%s</ul>
                </section>";

            // Remove unnecessary tab/space characters at the line beginnings.
            $html = self::reduceWhitespace($html);
            $w->write(sprintf($html, $excerptHtml));
        }
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
        $writer->write('</div></body></html>');
    }

    /**
     * Return array of lines from a specified file:line, optionally with extra lines around
     * for additional cognitive context.
     *
     * @return array
     */
    protected static function getLineExcerpt($file, $lineNumber, $extra = 0)
    {
        if (!is_readable($file)) {
            return array();
        }

        $file = new \SplFileObject($file);

        // We have to subtract 1 to extract correct lines via SplFileObject.
        $line = max($lineNumber - 1 - $extra, 0);

        $result = array();

        if (!$file->eof()) {
            $file->seek($line);
            for ($i = 0; $i <= ($extra * 2); $i++) {
                $result[++$line] = trim((string)$file->current(), "\n");
                $file->next();
            }
        }

        return $result;
    }

    /**
     * Take a rule description text and try to decorate/stylize parts of it with HTML.
     * Based on self::$descHighlightRules config.
     *
     * @return string
     */
    protected static function colorize($message)
    {
        // Compile final regex, if not done already.
        if (!self::$compiledHighlightRegex) {
            $prepared = self::$descHighlightRules;
            array_walk($prepared, function (&$v, $k) {
                $v = "(?<{$k}>{$v['regex']})";
            });

            self::$compiledHighlightRegex = "#(" . implode('|', $prepared) . ")#";
        }

        $rules = self::$descHighlightRules;

        return preg_replace_callback(self::$compiledHighlightRegex, function ($x) use ($rules) {
            // Extract currently matched specification of highlighting (Match groups
            // are named and we can find out which is not empty.).
            $definition = array_keys(array_intersect_key($rules, array_filter($x)));
            $definition = reset($definition);

            return "<span class='hlt-info {$definition}'>{$x[0]}</span>";
        }, $message);
    }

    /**
     * Take a file path and return a bit of HTML where the basename is wrapped in styled <span>.
     *
     * @return string
     */
    protected static function highlightFile($path)
    {
        $file = substr(strrchr($path, "/"), 1);
        $dir = str_replace($file, null, $path);

        return $dir . "<span class='path-basename'>" . $file . '</span>';
    }

    /**
     * Render a pretty informational table and send the HTML to the writer.
     *
     * @return void
     */
    protected function writeTable($title, $itemsTitle, $items)
    {
        if (!$items) {
            return;
        }

        $writer = $this->getWriter();
        $rows = null;

        // We will need to calculate percentages and whatnot.
        $max = max($items);
        $sum = array_sum($items);

        foreach ($items as $name => $count) {
            // Calculate chart/bar's percentage width relative to the highest occuring item.
            $width = $max !== 0 ? $count / $max * 100 : 0; // Avoid division by zero.

            $bar = sprintf(
                '<div class="t-bar" style="width: %d%%; opacity: %.2f"></div>',
                $width,
                min(0.2 + $width / 100, 1) // Minimum opacity for the bar is 0.2.
            );

            $pct = $sum !== 0 ? sprintf('%.1f', $count / $sum * 100) : '-'; // Avoid division by zero.
            $rows .= "<tr>
                <td class='t-cnt'>$count</td>
                <td class='t-pct'>$pct %</td>
                <th class='t-n'>{$name}{$bar}</th>
            </tr>";
        }

        $header = "<thead><tr><th>Count</th><th>%</th><th>$itemsTitle</th></tr></thead>";
        $html = "<section><table><caption>$title</caption>{$header}{$rows}</table></section>";
        $writer->write(self::reduceWhitespace($html));
    }

    /**
     * Go through passed violations and count occurrences based on pre-specified conditions.
     *
     * @return array
     */
    protected static function sumUpViolations($violations)
    {
        $result = array(
            self::CATEGORY_PRIORITY => array(),
            self::CATEGORY_NAMESPACE => array(),
            self::CATEGORY_RULESET => array(),
            self::CATEGORY_RULE => array(),
        );

        foreach ($violations as $v) {
            // We use "ref" reference to make things somewhat easier to read.
            // Also, using a reference to non-existing array index doesn't throw a notice.

            if ($ns = $v->getNamespaceName()) {
                $ref = &$result[self::CATEGORY_NAMESPACE][$ns];
                $ref = isset($ref) ? $ref + 1 : 1;
            }

            $rule = $v->getRule();

            // Friendly priority -> Add a describing word to "just number".
            $friendlyPriority = self::$priorityTitles[$rule->getPriority()];
            $ref = &$result[self::CATEGORY_PRIORITY][$friendlyPriority];
            $ref = isset($ref) ? $ref + 1 : 1;

            $ref = &$result[self::CATEGORY_RULESET][$rule->getRuleSetName()];
            $ref = isset($ref) ? $ref + 1 : 1;

            $ref = &$result[self::CATEGORY_RULE][$rule->getName()];
            $ref = isset($ref) ? $ref + 1 : 1;
        }

        // Sort numbers in each category from high to low.
        foreach ($result as &$inner) {
            arsort($inner);
        }

        return $result;
    }

    /**
     * Reduces two and more whitespaces in a row to a single whitespace to conserve space.
     *
     * @return string
     */
    protected static function reduceWhitespace($input, $eol = true)
    {
        return preg_replace("#\s+#", " ", $input) . ($eol ? PHP_EOL : null);
    }
}
