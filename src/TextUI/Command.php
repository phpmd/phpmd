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

use Exception;
use PHPMD\Baseline\BaselineFileFinder;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Baseline\BaselineSetFactory;
use PHPMD\Baseline\BaselineValidator;
use PHPMD\Cache\ResultCacheEngineFactory;
use PHPMD\Cache\ResultCacheKeyFactory;
use PHPMD\Cache\ResultCacheStateFactory;
use PHPMD\Console\Output;
use PHPMD\Console\OutputInterface;
use PHPMD\Console\StreamOutput;
use PHPMD\PHPMD;
use PHPMD\Renderer\RendererFactory;
use PHPMD\Report;
use PHPMD\RuleSetFactory;
use PHPMD\Utility\Paths;
use PHPMD\Writer\StreamWriter;
use RuntimeException;
use TypeError;
use ValueError;

/**
 * This class provides a command line interface for PHPMD
 */
final class Command
{
    public function __construct(
        private readonly Output $output,
    ) {
    }

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
     * @throws Exception
     */
    public function run(CommandLineOptions $opts, RuleSetFactory $ruleSetFactory): ExitCode
    {
        if ($opts->hasVersion()) {
            fwrite(STDOUT, sprintf('PHPMD %s', $this->getVersion()) . PHP_EOL);

            return ExitCode::Success;
        }

        // Create a report stream
        $stream = $opts->getReportFile() ?: STDOUT;

        // Create renderer and configure output
        $renderer = $opts->createRenderer();
        $renderer->setWriter(new StreamWriter($stream));

        $renderers = [$renderer];

        foreach ($opts->getReportFiles() as $reportFormat => $reportFile) {
            $reportRenderer = $opts->createRenderer($reportFormat);
            $reportRenderer->setWriter(new StreamWriter($reportFile));

            $renderers[] = $reportRenderer;
        }

        // Configure baseline violations
        $report = null;
        $finder = new BaselineFileFinder($opts);
        $baselineFile = null;
        if ($opts->generateBaseline() === BaselineMode::Generate) {
            // overwrite any renderer with the baseline renderer
            $renderers = [
                RendererFactory::createBaselineRenderer(new StreamWriter((string) $finder->notNull()->find())),
            ];
        } elseif ($opts->generateBaseline() === BaselineMode::Update) {
            $baselineFile = (string) $finder->notNull()->existingFile()->find();
            $baseline = BaselineSetFactory::fromFile(Paths::getRealPath($baselineFile));
            $renderers = [RendererFactory::createBaselineRenderer(new StreamWriter($baselineFile))];
            $report = new Report(new BaselineValidator($baseline, BaselineMode::Update));
        } else {
            // try to locate a baseline file and read it
            $baselineFile = $finder->existingFile()->find();
            if ($baselineFile !== null) {
                $baseline = BaselineSetFactory::fromFile(Paths::getRealPath($baselineFile));
                $report = new Report(new BaselineValidator($baseline, BaselineMode::None));
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
                [
                    'coverage' => $opts->getCoverageReport(),
                ]
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

        $ignorePattern = $ruleSetFactory->getIgnorePattern($opts->getRuleSets());
        $ruleSetList = $ruleSetFactory->createRuleSets($opts->getRuleSets());

        $cwd = getcwd() ?: '';

        // Configure Result Cache Engine
        if ($opts->generateBaseline() === BaselineMode::None) {
            $cacheEngineFactory = new ResultCacheEngineFactory(
                $this->output,
                new ResultCacheKeyFactory($cwd, $baselineFile),
                new ResultCacheStateFactory()
            );
            $cacheEngine = $cacheEngineFactory->create($cwd, $opts, $ruleSetList);
            if ($cacheEngine) {
                $phpmd->setResultCache($cacheEngine);
            }
        }

        $phpmd->processFiles(
            $opts->getInputPath(),
            $ignorePattern,
            $renderers,
            $ruleSetList,
            $report ?? new Report()
        );

        if ($phpmd->hasErrors() && !$opts->ignoreErrorsOnExit()) {
            return ExitCode::Error;
        }

        if (
            $phpmd->hasViolations()
            && !$opts->ignoreViolationsOnExit()
            && $opts->generateBaseline() === BaselineMode::None
        ) {
            return ExitCode::Violation;
        }

        return ExitCode::Success;
    }

    /**
     * Returns the current version number.
     */
    private function getVersion(): string
    {
        $build = __DIR__ . '/../../CHANGELOG';

        $version = '@package_version@';
        if (file_exists($build)) {
            $changelog = file_get_contents($build, false, null, 0, 1024) ?: '';
            $version = preg_match('/phpmd-([\S]+)/', $changelog, $match) ? $match[1] : $version;
        }

        return $version;
    }

    /**
     * The main method that can be used by a calling shell script, the return
     * value can be used as exit code.
     *
     * @param string[] $args The raw command line arguments array.
     * @throws RuntimeException
     * @throws ValueError
     * @throws TypeError
     */
    public static function main(array $args): ExitCode
    {
        $options = null;

        try {
            $ruleSetFactory = new RuleSetFactory();
            $options = new CommandLineOptions($args, $ruleSetFactory->listAvailableRuleSets());
            $errorFile = $options->getErrorFile();
            $errorStream = new StreamWriter($errorFile ?: STDERR);
            $output = new StreamOutput($errorStream->getStream(), $options->getVerbosity());
            $command = new self($output);

            foreach ($options->getDeprecations() as $deprecation) {
                $output->write($deprecation . PHP_EOL . PHP_EOL);
            }

            $bootstrapFile = $options->getBootstrapFile();
            if (is_string($bootstrapFile) && file_exists($bootstrapFile)) {
                require_once $bootstrapFile;
            }

            $exitCode = $command->run($options, $ruleSetFactory);
            unset($errorStream);
        } catch (Exception $e) {
            $file = $options ? $options->getErrorFile() : null;
            $writer = new StreamWriter($file ?: STDERR);
            $writer->write($e->getMessage() . PHP_EOL);

            if ($options && $options->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $writer->write($e->getFile() . ':' . $e->getLine() . PHP_EOL);
                $writer->write($e->getTraceAsString() . PHP_EOL);
            }

            $exitCode = ExitCode::Exception;
        }

        return $exitCode;
    }
}
