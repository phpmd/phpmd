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
use PHPMD\Baseline\BaselineMode;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Renderer\Option\Color;
use PHPMD\Renderer\Option\Verbose;
use PHPMD\Renderer\RendererFactory;
use PHPMD\Renderer\RendererInterface;
use PHPMD\Rule;
use PHPMD\Rule\Naming\LongVariable;
use Symfony\Component\Console\Exception\InvalidArgumentException as InvalidSymfonyArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TypeError;
use ValueError;

/**
 * This is a helper class that collects the specified cli arguments and puts them
 * into accessible properties.
 */
#[SuppressWarnings(LongVariable::class)]
class CommandLineOptions
{
    /** The minimum rule priority. */
    private int $minimumPriority = Rule::LOWEST_PRIORITY;

    /** The maximum rule priority. */
    private int $maximumPriority = Rule::HIGHEST_PRIORITY;

    /**
     * A php source code filename or directory.
     *
     * @var list<string>
     */
    private array $inputPaths;

    /**
     * The specified report format.
     *
     * @var ?string
     */
    private $reportFormat;

    /** An optional script to load before running analysis */
    private ?string $bootstrap = null;

    /**
     * Additional report files.
     *
     * @var array<string, string>
     */
    private array $reportFiles = [];

    /**
     * Ruleset filenames.
     *
     * @var list<string>
     */
    private array $ruleSets;

    /** File name of a PHPUnit code coverage report. */
    private ?string $coverageReport = null;

    /**
     * An array of extensions for valid php source code filenames.
     *
     * @var list<string>
     */
    private array $extensions;

    /**
     * Patterns that are used to exclude directories.
     *
     * Use asterisks to exclude by pattern. For example *src/foo/*.php or *src/foo/*
     *
     * @var list<string>
     */
    private array $ignore = [];

    /**
     * Should PHPMD run in strict mode?
     *
     * @since 1.2.0
     */
    private bool $strict;

    /**
     * Should PHPMD exit without error code even if error is found?
     *
     * @since 2.10.0
     */
    private bool $ignoreErrorsOnExit = false;

    /** Should PHPMD exit without error code even if violation is found? */
    private bool $ignoreViolationsOnExit = false;

    /** Should PHPMD baseline the existing violations and write them to the $baselineFile */
    private BaselineMode $generateBaseline = BaselineMode::None;

    /**
     * The baseline source file to read the baseline violations from.
     * Defaults to the path of the (first) ruleset file as phpmd.baseline.xml
     */
    private ?string $baselineFile = null;

    /** Should PHPMD read or write the result cache state from the cache file */
    private bool $cacheEnabled = false;

    /** If set the path to read and write the result cache state from and to. */
    private string $cacheFile;

    /** Determine the cache strategy. */
    private ResultCacheStrategy $cacheStrategy = ResultCacheStrategy::Content;

    /** Specify how many extra lines are added to a code snippet */
    private int $extraLineInExcerpt;

    /** number of cores to use for parsing */
    private ?int $threads = null;

    /**
     * Constructs a new command line options instance.
     *
     * @throws InvalidArgumentException
     * @throws InvalidSymfonyArgumentException
     * @throws ValueError
     * @throws TypeError
     */
    public function __construct(InputInterface $input)
    {
        $this->minimumPriority = (int) $this->readInt($input, 'minimum-priority');
        $this->maximumPriority = (int) $this->readInt($input, 'maximum-priority');
        $this->bootstrap = $this->readString($input, 'bootstrap');
        $this->coverageReport = $this->readString($input, 'coverage');

        /** @var list<string> */
        $extensions = $input->getOption('suffixes');
        $this->extensions = $extensions;

        /** @var list<string> */
        $ignore = $input->getOption('exclude');
        $this->ignore = $ignore;
        $this->strict = (bool) $input->getOption('strict');
        $this->generateBaseline = $input->getOption('update-baseline') ? BaselineMode::Update : BaselineMode::None;
        if ($input->getOption('generate-baseline')) {
            $this->generateBaseline = BaselineMode::Generate;
        }
        $this->baselineFile = $this->readString($input, 'baseline-file');
        $this->cacheEnabled = (bool) $input->getOption('cache');
        $this->cacheFile = (string) $this->readString($input, 'cache-file');
        $this->cacheStrategy = ResultCacheStrategy::from($this->readString($input, 'cache-strategy') ?? 'content');
        $this->ignoreErrorsOnExit = (bool) $input->getOption('ignore-errors-on-exit');
        $this->ignoreViolationsOnExit = (bool) $input->getOption('ignore-violations-on-exit');
        foreach (['checkstyle', 'github', 'gitlab', 'html', 'json', 'sarif', 'text', 'xml'] as $type) {
            $value = $this->readString($input, 'reportfile-' . $type);
            if ($value) {
                $this->reportFiles[$type] = $value;
            }
        }
        $this->extraLineInExcerpt = (int) $this->readInt($input, 'extra-line-in-excerpt');

        /** @var list<string> */
        $rulesets = $input->getOption('ruleset');
        $this->ruleSets = $rulesets;
        $this->reportFormat = (string) $this->readString($input, 'format');

        /** @var list<string> */
        $paths = $input->getArgument('paths');
        $inputFile = $this->readString($input, 'input-file');
        if ($inputFile) {
            $paths = [...$paths, ...$this->readInputFile($inputFile)];
        }
        if (!$paths) {
            throw new InvalidArgumentException('At least one path must be specified to analyse.');
        }

        $this->inputPaths = $paths;

        if ($this->inputPaths === ['-']) {
            $this->inputPaths = ['php://stdin'];
        }

        $this->threads = $this->readInt($input, 'threads');
    }

    /**
     * Returns a php source code filename or directory.
     *
     * @return list<string>
     */
    public function getInputPaths(): array
    {
        return $this->inputPaths;
    }

    /**
     * Returns the specified report format.
     *
     * @return ?string
     */
    public function getReportFormat()
    {
        return $this->reportFormat;
    }

    /**
     * Returns the current bootstrap file (if available) to load extra resources before analysis.
     */
    public function getBootstrapFile(): ?string
    {
        return $this->bootstrap;
    }

    /**
     * Returns a hash with report files specified for different renderers. The
     * key represents the report format and the value the report file location.
     *
     * @return array<string, string>
     */
    public function getReportFiles(): array
    {
        return $this->reportFiles;
    }

    /**
     * Returns an array of ruleset filename or rulesets
     *
     * @return list<string>
     */
    public function getRuleSets(): array
    {
        return $this->ruleSets;
    }

    /**
     * Returns the minimum rule priority.
     */
    public function getMinimumPriority(): int
    {
        return $this->minimumPriority;
    }

    /**
     * Returns the maximum rule priority.
     */
    public function getMaximumPriority(): int
    {
        return $this->maximumPriority;
    }

    /**
     * Returns the file name of a supplied code coverage report or <b>NULL</b>
     * if the user has not supplied the --coverage option.
     */
    public function getCoverageReport(): ?string
    {
        return $this->coverageReport;
    }

    /**
     * Returns extensions for valid php source code filenames.
     *
     * @return list<string>
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * Returns patterns that are used to exclude directories.
     *
     * @return list<string>
     */
    public function getIgnore(): array
    {
        return $this->ignore;
    }

    public function getThreads(): ?int
    {
        return $this->threads;
    }

    /**
     * Was the <b>--strict</b> option passed to PHPMD's command line interface?
     *
     * @since 1.2.0
     */
    public function hasStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Should the current violations be baselined
     */
    public function generateBaseline(): BaselineMode
    {
        return $this->generateBaseline;
    }

    /**
     * The filepath of the baseline violations xml
     */
    public function baselineFile(): ?string
    {
        return $this->baselineFile;
    }

    public function isCacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    /**
     * The filepath to the result cache state file
     */
    public function cacheFile(): string
    {
        return $this->cacheFile;
    }

    /**
     * The caching strategy to determine if a file should be (re)inspected.
     */
    public function cacheStrategy(): ResultCacheStrategy
    {
        return $this->cacheStrategy;
    }

    /**
     * Was the <b>--ignore-errors-on-exit</b> passed to PHPMD's command line interface?
     *
     * @since 2.10.0
     */
    public function ignoreErrorsOnExit(): bool
    {
        return $this->ignoreErrorsOnExit;
    }

    /**
     * Was the <b>--ignore-violations-on-exit</b> passed to PHPMD's command line interface?
     */
    public function ignoreViolationsOnExit(): bool
    {
        return $this->ignoreViolationsOnExit;
    }

    /**
     * Specify how many extra lines are added to a code snippet
     */
    public function extraLineInExcerpt(): int
    {
        return $this->extraLineInExcerpt;
    }

    /**
     * Creates a report renderer instance based on the user's command line
     * argument.
     *
     * @throws InvalidArgumentException When the specified renderer does not exist.
     */
    public function createRenderer(OutputInterface $output, ?string $reportFormat = null): RendererInterface
    {
        $renderer = $this->createRendererWithoutOptions($reportFormat);

        if ($renderer instanceof Verbose) {
            $renderer->setVerbosityLevel($output->getVerbosity());
        }

        if ($renderer instanceof Color) {
            $renderer->setColored($output->isDecorated());
        }

        return $renderer;
    }

    /**
     * @throws InvalidArgumentException When the specified renderer does not exist.
     */
    private function createRendererWithoutOptions(?string $reportFormat = null): RendererInterface
    {
        $reportFormat = $reportFormat ?: $this->reportFormat ?: '';

        return (new RendererFactory())->getRenderer($reportFormat);
    }

    /**
     * This method takes the given input file, reads the newline separated paths
     * from that file and creates a comma separated string of the file paths. If
     * the given <b>$inputFile</b> not exists, this method will throw an
     * exception.
     *
     * @param string $inputFile Specified input file name.
     *
     * @return list<string>
     * @throws InvalidArgumentException If the specified input file does not exist.
     * @since 1.1.0
     */
    private function readInputFile(string $inputFile): array
    {
        $content = @file($inputFile);

        if ($content === false) {
            throw new InvalidArgumentException("Unable to load '{$inputFile}'.");
        }

        return array_map(trim(...), $content);
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidSymfonyArgumentException
     */
    private function readString(InputInterface $input, string $name): ?string
    {
        $valiue = $input->getOption($name);
        if ($valiue === null) {
            return null;
        }
        if (!is_string($valiue)) {
            throw new InvalidArgumentException("Invalid argument type for '{$name}'.");
        }

        return (string) $valiue;
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidSymfonyArgumentException
     */
    private function readInt(InputInterface $input, string $name): ?int
    {
        $valiue = $input->getOption($name);
        if ($valiue === null) {
            return null;
        }
        if (!is_int($valiue) && (!is_string($valiue) || !ctype_digit($valiue))) {
            throw new InvalidArgumentException("Invalid argument type for '{$name}'.");
        }

        return (int) $valiue;
    }
}
