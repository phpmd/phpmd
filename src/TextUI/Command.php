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
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Baseline\BaselineFileFinder;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Baseline\BaselineSetFactory;
use PHPMD\Baseline\BaselineValidator;
use PHPMD\Cache\ResultCacheEngineFactory;
use PHPMD\Cache\ResultCacheKeyFactory;
use PHPMD\Cache\ResultCacheStateFactory;
use PHPMD\PHPMD;
use PHPMD\ProgressListener;
use PHPMD\Renderer\GitHubRenderer;
use PHPMD\Renderer\RendererFactory;
use PHPMD\Renderer\RendererInterface;
use PHPMD\Report;
use PHPMD\Rule\Design\CouplingBetweenObjects;
use PHPMD\RuleSet;
use PHPMD\RuleSetFactory;
use PHPMD\Utility\Paths;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Yaml\Exception\ParseException;
use TypeError;
use ValueError;

/**
 * This class provides a command line interface for PHPMD
 */
#[AsCommand(
    name: 'analyze',
    description: 'Analyzes source code',
)]
#[SuppressWarnings(CouplingBetweenObjects::class)]
final class Command extends SymfonyCommand
{
    public const ERROR = 3;

    private ?string $mainScript = null;

    private ?string $workerCommandName = null;

    public function setMainScript(string $mainScript): void
    {
        $this->mainScript = $mainScript;
    }

    public function setWorkerCommandName(string $workerCommandName): void
    {
        $this->workerCommandName = $workerCommandName;
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParseException
     */
    protected function configure(): void
    {
        (new CommandConfigurator())->configure($this);
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
        $this->loadBootstrap($options);

        $renderers = $this->createRenderers($options, $output);

        $finder = new BaselineFileFinder($options);
        [$report, $baselineFile, $baselineRenderers] = $this->configureBaselineMode($options, $finder);
        $renderers = $baselineRenderers ?? $renderers;

        $ruleSetFactory = $this->createRuleSetFactory($options);
        $phpmd = $this->createPhpmd($options);

        $excludePatterns = $ruleSetFactory->getExcludePatterns($options->getRuleSets());
        $ruleSetList = $ruleSetFactory->createRuleSets($options->getRuleSets());

        $this->configureResultCache($phpmd, $output, $options, $ruleSetList, $baselineFile);

        $progressListener = $input->getOption('no-progress') ? null : new ProgressListener($output);

        $phpmd->processFiles(
            $options->getInputPaths(),
            $excludePatterns,
            $renderers,
            $ruleSetList,
            $report ?? new Report(),
            $progressListener
        );

        return $this->resolveExitCode($phpmd, $options);
    }

    private function loadBootstrap(CommandLineOptions $options): void
    {
        $bootstrapFile = $options->getBootstrapFile();
        if (is_string($bootstrapFile) && file_exists($bootstrapFile)) {
            require_once $bootstrapFile;
        }
    }

    /**
     * @return list<RendererInterface>
     * @throws InvalidArgumentException
     */
    private function createRenderers(CommandLineOptions $options, OutputInterface $output): array
    {
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

        // Auto-detect GitHub Actions and add annotation output
        $hasGitHubRenderer = $renderer instanceof GitHubRenderer
            || isset($options->getReportFiles()['github']);
        if (getenv('GITHUB_ACTIONS') === 'true' && !$hasGitHubRenderer) {
            $githubRenderer = new GitHubRenderer();
            $githubRenderer->setWriter(new StreamOutput(STDERR));
            $renderers[] = $githubRenderer;
        }

        return $renderers;
    }

    /**
     * @return array{0: ?Report, 1: ?string, 2: ?list<RendererInterface>}
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    private function configureBaselineMode(CommandLineOptions $options, BaselineFileFinder $finder): array
    {
        if ($options->generateBaseline() === BaselineMode::Generate) {
            // overwrite any renderer with the baseline renderer
            $baselineFile = (string) $finder->notNull()->find();
            $stream = fopen($baselineFile, 'wb');
            if (!$stream) {
                throw new InvalidArgumentException("Unable to write to: '{$baselineFile}'.");
            }
            $renderers = [
                RendererFactory::createBaselineRenderer(new StreamOutput($stream)),
            ];

            return [null, $baselineFile, $renderers];
        }

        if ($options->generateBaseline() === BaselineMode::Update) {
            $baselineFile = (string) $finder->notNull()->existingFile()->find();
            $baseline = BaselineSetFactory::fromFile(Paths::getRealPath($baselineFile));
            $stream = fopen($baselineFile, 'wb');
            if (!$stream) {
                throw new InvalidArgumentException("Unable to write to: '{$baselineFile}'.");
            }
            $renderers = [RendererFactory::createBaselineRenderer(new StreamOutput($stream))];
            $report = new Report(new BaselineValidator($baseline, BaselineMode::Update));

            return [$report, $baselineFile, $renderers];
        }

        // try to locate a baseline file and read it
        $baselineFile = $finder->existingFile()->find();
        $report = null;
        if ($baselineFile !== null) {
            $baseline = BaselineSetFactory::fromFile(Paths::getRealPath($baselineFile));
            $report = new Report(new BaselineValidator($baseline, BaselineMode::None));
        }

        return [$report, $baselineFile, null];
    }

    private function createRuleSetFactory(CommandLineOptions $options): RuleSetFactory
    {
        $ruleSetFactory = new RuleSetFactory();
        $ruleSetFactory->setMinimumPriority($options->getMinimumPriority());
        $ruleSetFactory->setMaximumPriority($options->getMaximumPriority());
        if ($options->hasStrict()) {
            $ruleSetFactory->setStrict();
        }

        return $ruleSetFactory;
    }

    private function createPhpmd(CommandLineOptions $options): PHPMD
    {
        $phpmd = new PHPMD();
        $phpmd->setOptions(
            array_filter(
                [
                    'coverage' => $options->getCoverageReport(),
                ]
            )
        );

        $phpmd->setFileExtensions($options->getExtensions());
        $phpmd->addExcludePatterns($options->getExcludePatterns());
        $phpmd->setThreads($options->getThreads());

        if (null !== $this->mainScript) {
            $phpmd->setMainScript($this->mainScript);
        }
        if (null !== $this->workerCommandName) {
            $phpmd->setWorkerCommandName($this->workerCommandName);
        }

        return $phpmd;
    }

    /**
     * @param RuleSet[] $ruleSetList
     */
    private function configureResultCache(
        PHPMD $phpmd,
        OutputInterface $output,
        CommandLineOptions $options,
        array $ruleSetList,
        ?string $baselineFile,
    ): void {
        if ($options->generateBaseline() !== BaselineMode::None) {
            return;
        }

        $cwd = getcwd() ?: '';
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

    private function resolveExitCode(PHPMD $phpmd, CommandLineOptions $options): int
    {
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
        if (preg_match('/\d+\.\d+\.\d+/', $version) !== 1 && file_exists($build)) {
            $changelog = file_get_contents($build, false, null, 0, 1024) ?: '';
            $version = preg_match('/phpmd-([\S]+)/', $changelog, $match) ? $match[1] : $version;
        }

        return $version;
    }
}
