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

namespace PHPMD\TextUI;

use PHPMD\RuleSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Write detailed diagnostics to the output.
 *
 * Nothing is written below `-vv`. At `-vv` the effective configuration and
 * the exit code are reported, at `-vvv` additionally every loaded rule and the
 * full list of arguments and options as the command received them.
 */
final class DiagnosticsPrinter
{
    public function __construct(
        private readonly OutputInterface $output,
    ) {
    }

    /**
     * Prints every argument and option the command received, including
     * defaults, at debug verbosity.
     */
    public function printInput(InputInterface $input): void
    {
        if (!$this->output->isDebug()) {
            return;
        }

        $this->output->writeln('Arguments:');
        foreach ($input->getArguments() as $name => $value) {
            $this->output->writeln(sprintf('  %s: %s', $name, $this->formatValue($value)));
        }

        $this->output->writeln('Options:');
        foreach ($input->getOptions() as $name => $value) {
            $this->output->writeln(sprintf('  --%s: %s', $name, $this->formatValue($value)));
        }
    }

    /**
     * Prints the effective configuration at very verbose verbosity: what is
     * scanned, what is excluded, which rule sets (and at debug verbosity,
     * which rules) were loaded and which baseline file is in use.
     *
     * @param list<RuleSet> $ruleSetList
     * @param list<string> $excludePatterns
     */
    public function printConfiguration(
        CommandLineOptions $options,
        array $ruleSetList,
        array $excludePatterns,
        ?string $baselineFile,
    ): void {
        if (!$this->output->isVeryVerbose()) {
            return;
        }

        $this->output->writeln('Paths: ' . implode(', ', $options->getInputPaths()));
        $this->output->writeln('Exclude patterns: ' . implode(', ', $excludePatterns));
        $this->output->writeln('File suffixes: ' . implode(', ', $options->getExtensions()));
        $this->output->writeln('Threads: ' . ($options->getThreads() ?? 'auto (number of CPU cores)'));
        $this->output->writeln('Strict mode: ' . ($options->hasStrict() ? 'enabled' : 'disabled'));
        $this->output->writeln('Baseline file: ' . ($baselineFile ?? 'none'));

        $ruleCount = 0;
        foreach ($ruleSetList as $ruleSet) {
            $ruleCount += count($ruleSet->getRules());
        }
        $this->output->writeln(sprintf('Rule sets (%d rules):', $ruleCount));

        foreach ($ruleSetList as $ruleSet) {
            $rules = $ruleSet->getRules();
            $this->output->writeln(
                sprintf('  %s (%s, %d rules)', $ruleSet->getName(), $ruleSet->getFileName(), count($rules))
            );
            foreach ($rules as $rule) {
                $this->output->writeln(
                    sprintf('    %s (priority %d)', $rule->getName(), $rule->getPriority()),
                    OutputInterface::VERBOSITY_DEBUG
                );
            }
        }
    }

    public function printExitCode(int $exitCode): void
    {
        $this->output->writeln('Exit code: ' . $exitCode, OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    private function formatValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_array($value)) {
            return '[' . implode(', ', array_map($this->formatValue(...), $value)) . ']';
        }

        return is_scalar($value) ? (string) $value : get_debug_type($value);
    }
}
