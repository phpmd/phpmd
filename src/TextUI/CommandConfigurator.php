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

use InvalidArgumentException;
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Rule;
use PHPMD\Rule\Controversial\Superglobals;
use PHPMD\RuleSetFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Declares all CLI arguments and options for the {@link Command} class.
 */
final class CommandConfigurator
{
    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParseException
     */
    public function configure(SymfonyCommand $command): void
    {
        $ruleSetFactory = new RuleSetFactory();
        $renderers = $this->getListOfAvailableRenderers();
        $defaultConfig = $this->resolveDefaultConfig();

        $this->configurePathsAndRules($command, $ruleSetFactory, $defaultConfig, $renderers);
        $this->configureExecutionOptions($command, $ruleSetFactory, $defaultConfig);
        $this->configureCacheOptions($command, $ruleSetFactory, $defaultConfig);
        $this->configureBaselineOptions($command, $ruleSetFactory, $defaultConfig);
        $this->configureReportOptions($command);
        $this->configureMiscOptions($command, $ruleSetFactory, $defaultConfig);
    }

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
     * @return ?list<string>
     */
    #[SuppressWarnings(Superglobals::class)]
    private function resolveDefaultConfig(): ?array
    {
        $defaultConfig = $this->getDefaultConfig();

        /** @var list<string> */
        $argv = $_SERVER['argv'];
        $rules = [];
        foreach ($argv as $index => $arg) {
            if (str_starts_with($arg, '--ruleset=')) {
                $rules[] = substr($arg, 10);
            } elseif ($arg === '--ruleset' && isset($argv[$index + 1])) {
                $rules[] = $argv[$index + 1];
            }
        }

        return $rules ?: $defaultConfig;
    }

    /**
     * @param ?list<string> $defaultConfig
     * @param list<string> $renderers
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParseException
     */
    private function configurePathsAndRules(
        SymfonyCommand $command,
        RuleSetFactory $ruleSetFactory,
        ?array $defaultConfig,
        array $renderers,
    ): void {
        $availableRuleSets = $ruleSetFactory->listAvailableRuleSets();

        $paths = $defaultConfig ? $ruleSetFactory->getPaths($defaultConfig) : [];
        $command->addArgument(
            'paths',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'A php source code filename or directory, or "-" to scan stdin',
            $paths
        );
        $format = $defaultConfig ? $ruleSetFactory->getFormat($defaultConfig) : null;
        $defaultRenderer = $format ?? 'text';
        if (!in_array($defaultRenderer, $renderers, true)) {
            $defaultRenderer = reset($renderers);
        }
        $command->addOption(
            'format',
            null,
            InputOption::VALUE_REQUIRED,
            'A report format. One of: ' . implode(', ', $renderers),
            $defaultRenderer,
            $renderers
        );
        $command->addOption(
            'ruleset',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'A ruleset filename or a comma-separated string of rulesetfilenames.',
            $defaultConfig ?? $availableRuleSets,
            $availableRuleSets
        );
        $minimumPriority = $defaultConfig ? $ruleSetFactory->getMinimumPriority($defaultConfig) : null;
        $command->addOption(
            'minimum-priority',
            null,
            InputOption::VALUE_REQUIRED,
            'Rule priority threshold; rules with lower priority than this will not be used',
            $minimumPriority ?? Rule::LOWEST_PRIORITY
        );
        $maximumPriority = $defaultConfig ? $ruleSetFactory->getMaximumPriority($defaultConfig) : null;
        $command->addOption(
            'maximum-priority',
            null,
            InputOption::VALUE_REQUIRED,
            'Rule priority threshold; rules with higher priority than this will not be used',
            $maximumPriority ?? Rule::HIGHEST_PRIORITY
        );
        $suffixes = $defaultConfig ? $ruleSetFactory->getSuffixes($defaultConfig) : [];
        $command->addOption(
            'suffixes',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Source code filename extensions',
            $suffixes ?: ['php', 'php3', 'php4', 'php5', 'inc']
        );
        $command->addOption(
            'exclude',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Pattern that are used to ignore directories. Use asterisks to exclude by pattern. For example *src/foo/*.php or *src/foo/*',
            ['.git', '.svn', 'CVS', '.bzr', '.hg', 'SCCS']
        );
    }

    /**
     * @param ?list<string> $defaultConfig
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParseException
     */
    private function configureExecutionOptions(
        SymfonyCommand $command,
        RuleSetFactory $ruleSetFactory,
        ?array $defaultConfig,
    ): void {
        $command->addOption(
            'strict',
            null,
            InputOption::VALUE_NONE | InputOption::VALUE_NEGATABLE,
            'Also report those nodes with a SuppressWarnings attribute'
        );
        $command->addOption(
            'ignore-errors-on-exit',
            null,
            InputOption::VALUE_NONE,
            'Will exit with a zero code, even on error'
        );
        $command->addOption(
            'ignore-violations-on-exit',
            null,
            InputOption::VALUE_NONE,
            'Will exit with a zero code, even if any violations are found'
        );
        $command->addOption('input-file', null, InputOption::VALUE_REQUIRED, 'A file containing paths to analyze');
        $command->addOption('no-progress', null, InputOption::VALUE_NONE, 'Do not show progress bar, only results');
        $threads = $defaultConfig ? $ruleSetFactory->getThreads($defaultConfig) : null;
        $command->addOption(
            'threads',
            null,
            InputOption::VALUE_REQUIRED,
            'Number of threads to use for parsing',
            $threads
        );
    }

    /**
     * @param ?list<string> $defaultConfig
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParseException
     */
    private function configureCacheOptions(
        SymfonyCommand $command,
        RuleSetFactory $ruleSetFactory,
        ?array $defaultConfig,
    ): void {
        $cache = $defaultConfig ? $ruleSetFactory->isCacheEnabled($defaultConfig) : false;
        $command->addOption('cache', null, InputOption::VALUE_NEGATABLE, 'Will enable the result cache.', $cache);
        $cacheFile = $defaultConfig ? $ruleSetFactory->getCacheFile($defaultConfig) : null;
        $command->addOption(
            'cache-file',
            null,
            InputOption::VALUE_REQUIRED,
            'Result cache file to use.',
            $cacheFile ?? '.phpmd.result-cache.php'
        );
        $cacheStrategy = $defaultConfig ? $ruleSetFactory->getCacheStrategy($defaultConfig) : null;
        $cacheStrategies = [ResultCacheStrategy::Content->value, ResultCacheStrategy::Timestamp->value];
        if (!in_array($cacheStrategy, $cacheStrategies, true)) {
            $cacheStrategy = reset($cacheStrategies);
        }
        $command->addOption(
            'cache-strategy',
            null,
            InputOption::VALUE_REQUIRED,
            'Sets the caching strategy to determine if a file is still fresh. Either `content` to base it on the file contents, or `timestamp` to base it on the file modified timestamp',
            $cacheStrategy,
            $cacheStrategies
        );
    }

    /**
     * @param ?list<string> $defaultConfig
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParseException
     */
    private function configureBaselineOptions(
        SymfonyCommand $command,
        RuleSetFactory $ruleSetFactory,
        ?array $defaultConfig,
    ): void {
        $command->addOption(
            'generate-baseline',
            null,
            InputOption::VALUE_NONE,
            'Will generate a phpmd.baseline.xml next to the first ruleset file location'
        );
        $command->addOption(
            'update-baseline',
            null,
            InputOption::VALUE_NONE,
            'Will remove any non-existing violations from the phpmd.baseline.xml'
        );
        $baselineFile = $defaultConfig ? $ruleSetFactory->getBaseLineFile($defaultConfig) : null;
        $command->addOption(
            'baseline-file',
            null,
            InputOption::VALUE_REQUIRED,
            'A custom location of the baseline file',
            $baselineFile
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    private function configureReportOptions(SymfonyCommand $command): void
    {
        $command->addOption(
            'extra-line-in-excerpt',
            null,
            InputOption::VALUE_REQUIRED,
            'Specify how many extra lines are added to a code snippet in html format',
            2
        );
        $command->addOption(
            'coverage',
            null,
            InputOption::VALUE_REQUIRED,
            'Clover style CodeCoverage report, as produced by PHPUnit\'s --coverage-clover option.'
        );
        $command->addOption(
            'reportfile-checkstyle',
            null,
            InputOption::VALUE_REQUIRED,
            'Write report to a checkstyle file'
        );
        $command->addOption('reportfile-github', null, InputOption::VALUE_REQUIRED, 'Write report to a GitHub file');
        $command->addOption(
            'reportfile-githubcheckruns',
            null,
            InputOption::VALUE_REQUIRED,
            'Write report to a GitHub Check Runs file'
        );
        $command->addOption('reportfile-gitlab', null, InputOption::VALUE_REQUIRED, 'Write report to a GitLab file');
        $command->addOption('reportfile-html', null, InputOption::VALUE_REQUIRED, 'Write report to a html file');
        $command->addOption('reportfile-json', null, InputOption::VALUE_REQUIRED, 'Write report to a json file');
        $command->addOption('reportfile-sarif', null, InputOption::VALUE_REQUIRED, 'Write report to a sarif file');
        $command->addOption('reportfile-text', null, InputOption::VALUE_REQUIRED, 'Write report to a text file');
        $command->addOption('reportfile-xml', null, InputOption::VALUE_REQUIRED, 'Write report to an xml file');
    }

    /**
     * @param ?list<string> $defaultConfig
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParseException
     */
    private function configureMiscOptions(
        SymfonyCommand $command,
        RuleSetFactory $ruleSetFactory,
        ?array $defaultConfig,
    ): void {
        $bootstrap = $defaultConfig ? $ruleSetFactory->getBoostrap($defaultConfig) : null;
        $command->addOption(
            'bootstrap',
            null,
            InputOption::VALUE_REQUIRED,
            'An optional script to load before running analysis',
            $bootstrap
        );
    }

    /**
     * Get a list of available renderers
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
}
