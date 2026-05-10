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
use InvalidArgumentException;
use PHPMD\Baseline\BaselineFileFinder;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Baseline\BaselineSetFactory;
use PHPMD\Baseline\BaselineValidator;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Cache\ResultCacheEngineFactory;
use PHPMD\Cache\ResultCacheKeyFactory;
use PHPMD\Cache\ResultCacheStateFactory;
use PHPMD\PHPMD;
use PHPMD\ProgressListener;
use PHPMD\Renderer\RendererFactory;
use PHPMD\Report;
use PHPMD\Rule;
use PHPMD\RuleSetFactory;
use PHPMD\Utility\Paths;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use TypeError;
use ValueError;

/**
 * This class provides a command line interface for PHPMD
 */
#[AsCommand(
    name: 'analyze',
    description: 'Analyzes source code',
)]
final class Command extends SymfonyCommand
{
    public const ERROR = 3;

    /**
     * @return ?list<string>
     */
    private function getDefaultConfig(): ?array
    {
        // Files to be used as config automatically
        // Ordered by priority
        $files = [
            'phpmd.yml',
            'phpmd.yaml',
            'phpmd.json',
            'phpmd.xml',
            'phpmd.php',
        ];

        foreach ($files as $file) {
            // Search for phpmd.yml, .phpmd.yml and phpmd.yml.dist
            foreach ([$file, ".$file", "$file.dist"] as $path) {
                if (file_exists($path)) {
                    return [$path];
                }
            }
        }

        return null;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $ruleSetFactory = new RuleSetFactory();
        $availableRuleSets = $ruleSetFactory->listAvailableRuleSets();
        $renderers = $this->getListOfAvailableRenderers();

        $this->addArgument(
            'paths',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'A php source code filename or directory, or "-" to scan stdin'
        );
        $defaultRenderer = 'text';
        if (!in_array('text', $renderers, true)) {
            $defaultRenderer = reset($renderers);
        }
        $this->addOption(
            'format',
            null,
            InputOption::VALUE_REQUIRED,
            'A report format. One of: ' . implode(', ', $renderers),
            $defaultRenderer,
            $renderers
        );
        $this->addOption(
            'ruleset',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'A ruleset filename or a comma-separated string of rulesetfilenames.',
            $this->getDefaultConfig() ?? $availableRuleSets,
            $availableRuleSets
        );
        $this->addOption(
            'minimum-priority',
            null,
            InputOption::VALUE_REQUIRED,
            'Rule priority threshold; rules with lower priority than this will not be used',
            Rule::LOWEST_PRIORITY
        );
        $this->addOption(
            'maximum-priority',
            null,
            InputOption::VALUE_REQUIRED,
            'Rule priority threshold; rules with higher priority than this will not be used',
            Rule::HIGHEST_PRIORITY
        );
        $this->addOption(
            'suffixes',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Source code filename extensions',
            ['php', 'php3', 'php4', 'php5', 'inc']
        );
        $this->addOption(
            'exclude',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Pattern that are used to ignore directories. Use asterisks to exclude by pattern. For example *src/foo/*.php or *src/foo/*',
            ['.git', '.svn', 'CVS', '.bzr', '.hg', 'SCCS']
        );
        $this->addOption(
            'strict',
            null,
            InputOption::VALUE_NONE | InputOption::VALUE_NEGATABLE,
            'Also report those nodes with a SuppressWarnings attribute'
        );
        $this->addOption(
            'ignore-errors-on-exit',
            null,
            InputOption::VALUE_NONE,
            'Will exit with a zero code, even on error'
        );
        $this->addOption(
            'ignore-violations-on-exit',
            null,
            InputOption::VALUE_NONE,
            'Will exit with a zero code, even if any violations are found'
        );
        $this->addOption('cache', null, InputOption::VALUE_NONE, 'Will enable the result cache.');
        $this->addOption(
            'cache-file',
            null,
            InputOption::VALUE_REQUIRED,
            'Result cache file to use.',
            '.phpmd.result-cache.php'
        );
        $this->addOption(
            'cache-strategy',
            null,
            InputOption::VALUE_REQUIRED,
            'Sets the caching strategy to determine if a file is still fresh. Either `content` to base it on the file contents, or `timestamp` to base it on the file modified timestamp',
            ResultCacheStrategy::Content->value,
            [ResultCacheStrategy::Content->value, ResultCacheStrategy::Timestamp->value]
        );
        $this->addOption(
            'generate-baseline',
            null,
            InputOption::VALUE_NONE,
            'Will generate a phpmd.baseline.xml next to the first ruleset file location'
        );
        $this->addOption(
            'update-baseline',
            null,
            InputOption::VALUE_NONE,
            'Will remove any non-existing violations from the phpmd.baseline.xml'
        );
        $this->addOption('baseline-file', null, InputOption::VALUE_REQUIRED, 'A custom location of the baseline file');
        $this->addOption(
            'extra-line-in-excerpt',
            null,
            InputOption::VALUE_REQUIRED,
            'Specify how many extra lines are added to a code snippet in html format',
            2
        );
        $this->addOption(
            'coverage',
            null,
            InputOption::VALUE_REQUIRED,
            'Clover style CodeCoverage report, as produced by PHPUnit\'s --coverage-clover option.'
        );
        $this->addOption(
            'reportfile-checkstyle',
            null,
            InputOption::VALUE_REQUIRED,
            'Write report to a checkstyle file'
        );
        $this->addOption('reportfile-github', null, InputOption::VALUE_REQUIRED, 'Write report to a GitHub file');
        $this->addOption('reportfile-gitlab', null, InputOption::VALUE_REQUIRED, 'Write report to a GitLab file');
        $this->addOption('reportfile-html', null, InputOption::VALUE_REQUIRED, 'Write report to a html file');
        $this->addOption('reportfile-json', null, InputOption::VALUE_REQUIRED, 'Write report to a json file');
        $this->addOption('reportfile-sarif', null, InputOption::VALUE_REQUIRED, 'Write report to a sarif file');
        $this->addOption('reportfile-text', null, InputOption::VALUE_REQUIRED, 'Write report to a text file');
        $this->addOption('reportfile-xml', null, InputOption::VALUE_REQUIRED, 'Write report to an xml file');
        $this->addOption(
            'bootstrap',
            null,
            InputOption::VALUE_REQUIRED,
            'An optional script to load before running analysis'
        );
        $this->addOption('input-file', null, InputOption::VALUE_REQUIRED, 'A file containing paths to analyze');
        $this->addOption('no-progress', null, InputOption::VALUE_NONE, 'Do not show progress bar, only results');
        $this->addOption('threads', null, InputOption::VALUE_REQUIRED, 'Number of threads to use for parsing');
    }

    /**
     * Get a list of available renderers
     *
     * @return string|null The list of renderers found separated by comma, or null if none.
     *
     * @return list<string>
     * @throws InvalidArgumentException
     */
    private function getListOfAvailableRenderers(): array
    {
        $renderersDirPathName = __DIR__ . '/../Renderer';
        $renderers = [];

        $filesPaths = scandir($renderersDirPathName);
        if ($filesPaths === false) {
            throw new InvalidArgumentException("Unable to access directory: '{$renderersDirPathName}'.");
        }

        foreach ($filesPaths as $rendererFileName) {
            $rendererName = [];
            if (preg_match('/^(\w+)Renderer.php$/i', $rendererFileName, $rendererName)) {
                $renderers[] = strtolower($rendererName[1]);
            }
        }

        sort($renderers);

        return $renderers;
    }

    /**
     * @throws InvalidArgumentException
     * @throws TypeError
     * @throws ValueError
     * @throws RuntimeException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = new CommandLineOptions($input);

        $bootstrapFile = $options->getBootstrapFile();
        if (is_string($bootstrapFile) && file_exists($bootstrapFile)) {
            require_once $bootstrapFile;
        }

        // Create renderer and configure output
        $renderer = $options->createRenderer($output);
        $renderer->setWriter($output);
        $renderers = [$renderer];

        foreach ($options->getReportFiles() as $reportFormat => $reportFile) {
            $reportRenderer = $options->createRenderer($output, $reportFormat);
            $stream = fopen($reportFile, 'wb');
            if (!$stream) {
                throw new InvalidArgumentException("Unable to write to: '{$reportFile}'.");
            }
            $reportRenderer->setWriter(new StreamOutput($stream));

            $renderers[] = $reportRenderer;
        }

        // Configure baseline violations
        $report = null;
        $finder = new BaselineFileFinder($options);
        $baselineFile = null;
        if ($options->generateBaseline() === BaselineMode::Generate) {
            // overwrite any renderer with the baseline renderer
            $baseLineFile = (string) $finder->notNull()->find();
            $stream = fopen($baseLineFile, 'wb');
            if (!$stream) {
                throw new InvalidArgumentException("Unable to write to: '{$baseLineFile}'.");
            }
            $renderers = [
                RendererFactory::createBaselineRenderer(new StreamOutput($stream)),
            ];
        } elseif ($options->generateBaseline() === BaselineMode::Update) {
            $baselineFile = (string) $finder->notNull()->existingFile()->find();
            $baseline = BaselineSetFactory::fromFile(Paths::getRealPath($baselineFile));
            $stream = fopen($baselineFile, 'wb');
            if (!$stream) {
                throw new InvalidArgumentException("Unable to write to: '{$baselineFile}'.");
            }
            $renderers = [RendererFactory::createBaselineRenderer(new StreamOutput($stream))];
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
        $ruleSetFactory = new RuleSetFactory();
        $ruleSetFactory->setMinimumPriority($options->getMinimumPriority());
        $ruleSetFactory->setMaximumPriority($options->getMaximumPriority());
        if ($options->hasStrict()) {
            $ruleSetFactory->setStrict();
        }

        $phpmd = new PHPMD();
        $phpmd->setOptions(
            array_filter(
                [
                    'coverage' => $options->getCoverageReport(),
                ]
            )
        );

        $phpmd->setFileExtensions($options->getExtensions());
        $phpmd->addIgnorePatterns($options->getIgnore());
        $phpmd->setThreads($options->getThreads());

        $ignorePattern = $ruleSetFactory->getIgnorePattern($options->getRuleSets());
        $ruleSetList = $ruleSetFactory->createRuleSets($options->getRuleSets());

        $cwd = getcwd() ?: '';

        // Configure Result Cache Engine
        if ($options->generateBaseline() === BaselineMode::None) {
            $cacheEngineFactory = new ResultCacheEngineFactory(
                $output,
                new ResultCacheKeyFactory($cwd, $baselineFile),
                new ResultCacheStateFactory()
            );
            $cacheEngine = $cacheEngineFactory->create($cwd, $options, $ruleSetList);
            if ($cacheEngine) {
                $phpmd->setResultCache($cacheEngine);
            }
        }

        $progressListener = null;
        if (!$input->getOption('no-progress')) {
            $progressListener = new ProgressListener($output);
        }

        $phpmd->processFiles(
            $options->getInputPaths(),
            $ignorePattern,
            $renderers,
            $ruleSetList,
            $report ?? new Report(),
            $progressListener
        );

        if ($phpmd->hasErrors() && !$options->ignoreErrorsOnExit()) {
            return self::ERROR;
        }

        if (
            $phpmd->hasViolations()
            && !$options->ignoreViolationsOnExit()
            && $options->generateBaseline() === BaselineMode::None
        ) {
            return self::INVALID;
        }

        return self::SUCCESS;
    }

    /**
     * Returns the current version number.
     */
    public static function getVersion(): string
    {
        $build = __DIR__ . '/../../CHANGELOG';

        $version = '@package_version@';
        if (file_exists($build)) {
            $changelog = file_get_contents($build, false, null, 0, 1024) ?: '';
            $version = preg_match('/phpmd-([\S]+)/', $changelog, $match) ? $match[1] : $version;
        }

        return $version;
    }
}
