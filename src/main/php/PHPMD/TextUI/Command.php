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

use PHPMD\Baseline\BaselineFileFinder;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Baseline\BaselineSetFactory;
use PHPMD\Baseline\BaselineValidator;
use PHPMD\PHPMD;
use PHPMD\Renderer\RendererFactory;
use PHPMD\Report;
use PHPMD\RuleSetFactory;
use PHPMD\Utility\Paths;
use PHPMD\Writer\StreamWriter;

/**
 * This class provides a command line interface for PHPMD
 */
class Command
{
    /**
     * Exit codes used by the phpmd command line tool.
     */
    const EXIT_SUCCESS = 0,
        EXIT_EXCEPTION = 1,
        EXIT_VIOLATION = 2,
        EXIT_ERROR = 3;

    /**
     * This method creates a PHPMD instance and configures this object based
     * on the user's input, then it starts the source analysis.
     *
     * The return value of this method can be used as an exit code. A value
     * equal to <b>EXIT_SUCCESS</b> means that no violations or errors were
     * found in the analyzed code. Otherwise this method will return a value
     * equal to <b>EXIT_VIOLATION</b> or <b>EXIT_ERROR</b> respectively.
     *
     * The use of the flags <b>--ignore-violations-on-exit</b> and
     * <b>--ignore-errors-on-exit</b> will result to a <b>EXIT_SUCCESS</b>
     * even if any violation or error is found.
     *
     * @param \PHPMD\TextUI\CommandLineOptions $opts
     * @param \PHPMD\RuleSetFactory            $ruleSetFactory
     * @return integer
     */
    public function run(CommandLineOptions $opts, RuleSetFactory $ruleSetFactory)
    {
        if ($opts->hasVersion()) {
            fwrite(STDOUT, sprintf('PHPMD %s', $this->getVersion()) . PHP_EOL);

            return self::EXIT_SUCCESS;
        }

        // Create a report stream
        $stream = $opts->getReportFile() ? $opts->getReportFile() : STDOUT;

        // Create renderer and configure output
        $renderer = $opts->createRenderer();
        $renderer->setWriter(new StreamWriter($stream));

        $renderers = array($renderer);

        foreach ($opts->getReportFiles() as $reportFormat => $reportFile) {
            $reportRenderer = $opts->createRenderer($reportFormat);
            $reportRenderer->setWriter(new StreamWriter($reportFile));

            $renderers[] = $reportRenderer;
        }

        // Configure baseline violations
        $report   = null;
        $finder   = new BaselineFileFinder($opts);
        if ($opts->generateBaseline() === BaselineMode::GENERATE) {
            // overwrite any renderer with the baseline renderer
            $renderers = array(RendererFactory::createBaselineRenderer(new StreamWriter($finder->notNull()->find())));
        } elseif ($opts->generateBaseline() === BaselineMode::UPDATE) {
            $baselineFile = $finder->notNull()->existingFile()->find();
            $baseline     = BaselineSetFactory::fromFile(Paths::getRealPath($baselineFile));
            $renderers    = array(RendererFactory::createBaselineRenderer(new StreamWriter($baselineFile)));
            $report       = new Report(new BaselineValidator($baseline, BaselineMode::UPDATE));
        } else {
            // try to locate a baseline file and read it
            $baselineFile = $finder->existingFile()->find();
            if ($baselineFile !== null) {
                $baseline = BaselineSetFactory::fromFile(Paths::getRealPath($baselineFile));
                $report   = new Report(new BaselineValidator($baseline, BaselineMode::NONE));
            }
        }

        // Configure a rule set factory
        $ruleSetFactory->setMinimumPriority($opts->getMinimumPriority());
        $ruleSetFactory->setMaximumPriority($opts->getMaximumPriority());
        if ($opts->hasStrict()) {
            $ruleSetFactory->setStrict();
        }

        $phpmd = new PHPMD();
        $phpmd->setOptions(
            array_filter(
                array(
                    'coverage' => $opts->getCoverageReport(),
                )
            )
        );

        $extensions = $opts->getExtensions();
        if ($extensions !== null) {
            $phpmd->setFileExtensions(explode(',', $extensions));
        }

        $ignore = $opts->getIgnore();
        if ($ignore !== null) {
            $phpmd->addIgnorePatterns(explode(',', $ignore));
        }

        $phpmd->processFiles(
            $opts->getInputPath(),
            $opts->getRuleSets(),
            $renderers,
            $ruleSetFactory,
            $report !== null ? $report : new Report()
        );

        if ($phpmd->hasErrors() && !$opts->ignoreErrorsOnExit()) {
            return self::EXIT_ERROR;
        }

        if ($phpmd->hasViolations()
            && !$opts->ignoreViolationsOnExit()
            && $opts->generateBaseline() === BaselineMode::NONE) {
            return self::EXIT_VIOLATION;
        }

        return self::EXIT_SUCCESS;
    }

    /**
     * Returns the current version number.
     *
     * @return string
     */
    private function getVersion()
    {
        $build = __DIR__ . '/../../../../../build.properties';

        $version = '@package_version@';
        if (file_exists($build)) {
            $data    = @parse_ini_file($build);
            $version = $data['project.version'];
        }

        return $version;
    }

    /**
     * The main method that can be used by a calling shell script, the return
     * value can be used as exit code.
     *
     * @param string[] $args The raw command line arguments array.
     * @return integer
     */
    public static function main(array $args)
    {
        try {
            $ruleSetFactory = new RuleSetFactory();
            $options        = new CommandLineOptions($args, $ruleSetFactory->listAvailableRuleSets());
            $command        = new Command();

            $exitCode = $command->run($options, $ruleSetFactory);
        } catch (\Exception $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
            $exitCode = self::EXIT_EXCEPTION;
        }

        return $exitCode;
    }
}
