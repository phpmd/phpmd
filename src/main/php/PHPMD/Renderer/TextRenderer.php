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
use PHPMD\Console\OutputInterface;
use PHPMD\Renderer\Option\Color;
use PHPMD\Renderer\Option\Verbose;
use PHPMD\Report;

/**
 * This renderer output a textual log with all found violations and suspect
 * software artifacts.
 */
class TextRenderer extends AbstractRenderer implements Color, Verbose
{
    private int $columnSpacing = 2;

    private int $verbosityLevel = OutputInterface::VERBOSITY_NORMAL;

    private bool $colored = false;

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     */
    public function renderReport(Report $report): void
    {
        $writer = $this->getWriter();
        $longestLocationLength = 0;
        $longestRuleNameLength = 0;
        $violations = [];

        foreach ($report->getRuleViolations() as $violation) {
            $location = $violation->getFileName() . ':' . $violation->getBeginLine();
            $rule = $violation->getRule();
            $ruleName = $rule->getName();
            $ruleSet = $rule->getRuleSetName();
            $locationLength = mb_strlen($location);
            $ruleNameLength = mb_strlen($ruleName);
            $longestLocationLength = max($longestLocationLength, $locationLength);
            $longestRuleNameLength = max($longestRuleNameLength, $ruleNameLength);
            $violations[] = [$violation, $location, $ruleName, $ruleSet, $locationLength, $ruleNameLength];
        }

        foreach ($violations as $data) {
            [$violation, $location, $ruleName, $ruleSet, $locationLength, $ruleNameLength] = $data;

            if ($this->verbosityLevel < OutputInterface::VERBOSITY_VERBOSE) {
                $writer->write($location);
                $writer->write(str_repeat(' ', $longestLocationLength + $this->columnSpacing - $locationLength));
            }

            $writer->write($this->applyColor($ruleName, 'yellow'));
            $writer->write(str_repeat(' ', $longestRuleNameLength + $this->columnSpacing - $ruleNameLength));
            $writer->write($this->applyColor($violation->getDescription(), 'red'));

            if ($this->verbosityLevel >= OutputInterface::VERBOSITY_VERBOSE) {
                $writer->write(PHP_EOL);
                $writer->write('📁 in ' . preg_replace('/:(\d+)$/', ' on line $1', $location) . PHP_EOL);
                $set = preg_replace('/rules$/', '', strtolower(str_replace(' ', '', $ruleSet)));
                $url = 'https://phpmd.org/rules/' . $set . '.html#' . strtolower($ruleName);
                $writer->write('🔗 ' . $set . '.xml ' . $url . PHP_EOL);
            }

            $writer->write(PHP_EOL);
        }

        foreach ($report->getErrors() as $error) {
            $writer->write($error->getFile());
            $writer->write("\t-\t");
            $writer->write($error->getMessage());
            $writer->write(PHP_EOL);
        }
    }

    public function setVerbosityLevel(int $level): void
    {
        $this->verbosityLevel = $level;
    }

    public function setColored(bool $colored): void
    {
        $this->colored = $colored;
    }

    private function applyColor(string $text, string $color): string
    {
        if (!$this->colored) {
            return $text;
        }

        $colors = [
            'yellow' => 33,
            'red' => 31,
        ];
        $color = $colors[$color] ?? 0;

        return "\033[{$color}m{$text}\033[0m";
    }
}
