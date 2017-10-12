<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2017, Premysl Karbula <premavansmuuf@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Premysl Karbula <premavansmuuf@gmail.com>
 * @copyright 2017 Premysl Karbula. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;

/**
 * This renderer output a pretty html file with all found violations and suspect
 * software artifacts.
 *
 * @author Premysl Karbula <premavansmuuf@gmail.com>
 * @copyright 2017 Premysl Karbula. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class PrettyHTMLRenderer extends AbstractRenderer
{

	// Used in self::colorize() method.
	const INFO_HIGHLIGHTING = [
		'quoted' => [
			// Quoted strings.
			'regex' => '(["\'][^\'"]+["\'])',
			'css-class' => 'highlight-quoted',
		],
		'variable' => [
			// Variables.
			'regex' => '(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)',
			'css-class' => 'highlight-variable',
		],
		'method' => [
			// Method names.
			'regex' => '(method [a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\(.*\))?',
			'css-class' => 'highlight-method',
		],
	];

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

		$borderRadius = "border-radius: 0.5ex;";
		$borderTopRadius = "border-top-left-radius: 0.5ex; border-top-right-radius: 0.5ex;";
		$borderBottomRadius = "border-bottom-left-radius: 0.5ex; border-bottom-right-radius: 0.5ex;";

		// Avoid inlining styles.
		$style = "
			<style>

				body {
					font-family: sans-serif;
				}

				.excerpt {
					padding: 1ex;
					background: #fff;
					$borderBottomRadius
				}

				.code-line {
					position: relative;
					height: 1.2em;
					line-height: 1.2em;
					font-family: monospace;
					white-space: nowrap;
				}

				.code-line:nth-child(2n + 1) {
					background-color: rgba(175, 195, 202, 0.2)
				}

				.line-number {
					position: absolute;
					width: 5%;
					height: 100%;
					left: 0;
					text-align: right;
					border-right: 2px solid rgba(0, 0, 0, 0.2);
					padding-right: 1ex;
					box-sizing: border-box;
				}

				.line-content {
					position: absolute;
					padding-left: 1ex;
					white-space: pre-wrap;
					box-sizing: border-box;
					width: 95%;
					height: 100%;
					right: 0;
				}

				.line-highlight {
					background: #ffee99 !important
				}

				.violation {
					background: #afc3ca;
					padding: 1ex;
					margin-bottom: 2em;
					$borderRadius
				}

				.violation-info {
					background: rgba(255, 255, 255, 0.75);
					font-weight: bold;
					padding: 1ex;
					margin-bottom: 1ex;
					font-size: 0.9rem;
					$borderRadius
				}

				.path-basename {
					font-weight: bold;
				}

				.highlight-info {
					display: inline-block;
					$borderRadius
					padding: 2px 4px;
					font-style: italic;
				}

					.highlight-info.quoted {
						background-color: #92de71;
					}

					.highlight-info.variable {
						background-color: #a3d2ff;
					}

					.highlight-info.method {
						background-color: #f7c0ff;
					}

				.violation-file {
					padding: 1ex;
					background: #fffec9;
					font-size: 0.8rem;
					$borderTopRadius
				}

				.violation-link {
					font-style: italic;
				}

			</style>
		";

		$writer->write("<html><head>{$style}<title>PHPMD Report</title></head><body>");
		$writer->write(PHP_EOL);
		$writer->write('<h1>PHPMD report</h1>');
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

		$violations = $report->getRuleViolations();

		$w->write(sprintf('<h2>%d problems found</h2>', count($violations)));
		$w->write(PHP_EOL);

		foreach ($violations as $violation) {

			// Get excerpt of the code from validated file.
			$excerptHtml = null;
			$excerpt = self::getLineExcerpt(
				$violation->getFileName(),
				$violation->getBeginLine(),
				2
			);

			foreach ($excerpt as $line => $code) {
				$class = $line === $violation->getBeginLine() ? 'line-highlight' : null;
				$codeHtml = htmlentities($code);
				$excerptHtml .= "
					<div class='code-line {$class}'>
						<div class='line-number'>{$line}</div>
						<div class='line-content'>{$codeHtml}</div>
					</div>
				";
			}

			$infoHtml = self::colorize(htmlentities($violation->getDescription()));
			$fileHtml = self::highlightFile($violation->getFileName());
			$linkHtml = null;
			if ($url = $violation->getRule()->getExternalInfoUrl()) {
				$linkHtml = "<a class='violation-link' href='{$url}' target='_blank'>(info)</a>";
			}

			$html = "
				<div class='violation'>
					<header class='violation-info'>Problem {$linkHtml}: {$infoHtml}</header>
					<div class='violation-file'><b>File:</b> {$fileHtml}</div>
					<div class='excerpt'>{$excerptHtml}</div>
				</div>
			";

			$w->write($html);

		}

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

	/**
	 * Return array of lines from a specified file:line, optionally with extra lines around
	 * for additional cognitive context.
	 */
	protected static function getLineExcerpt($file, $lineNumber, $extra = 0)
	{
		$file = new \SplFileObject($file);

		// We have to subtract 1 to extract correct lines via SplFileObject.
		$line = $lineNumber - 1 - $extra;

		$result = [];

		if (!$file->eof()) {
			$file->seek($line);
			for ($i = 0; $i <= ($extra * 2); $i++) {
				$result[++$line] = trim($file->current(), "\n");
				$file->next();
			}
		}

		return $result;

	}

	protected static function colorize($message)
	{
		// Compile final regex, if not done already.
		if (!self::$compiledHighlightRegex) {

			$prepared = self::INFO_HIGHLIGHTING;
			array_walk($prepared, function(&$v, $k) {
				$v = "(?<{$k}>{$v['regex']})";
			});

			self::$compiledHighlightRegex = "#(" . implode('|', $prepared) . ")#";

		}

		return preg_replace_callback(self::$compiledHighlightRegex, function($x) {

			// Extract currently matched specification of highlighting (Match groups
			// are named and we can find out which is not empty.).
			$definition = array_keys(array_intersect_key(self::INFO_HIGHLIGHTING, array_filter($x)));
			$definition = reset($definition);

			return "<span class='highlight-info {$definition}'>{$x[0]}</span>";

		}, $message);

	}

	protected static function highlightFile($path)
	{

		$file = substr(strrchr($path, "/"), 1);
		$dir = str_replace($file, null, $path);

		return $dir . "<span class='path-basename'>" . $file . '</span>';

	}

}
