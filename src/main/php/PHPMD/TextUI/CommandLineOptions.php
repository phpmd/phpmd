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
use PHPMD\AbstractRenderer;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Console\OutputInterface;
use PHPMD\Renderer\AnsiRenderer;
use PHPMD\Renderer\CheckStyleRenderer;
use PHPMD\Renderer\GitHubRenderer;
use PHPMD\Renderer\GitLabRenderer;
use PHPMD\Renderer\HTMLRenderer;
use PHPMD\Renderer\JSONRenderer;
use PHPMD\Renderer\Option\Color;
use PHPMD\Renderer\Option\Verbose;
use PHPMD\Renderer\SARIFRenderer;
use PHPMD\Renderer\TextRenderer;
use PHPMD\Renderer\XMLRenderer;
use PHPMD\Rule;
use PHPMD\Utility\ArgumentsValidator;
use TypeError;
use ValueError;

/**
 * This is a helper class that collects the specified cli arguments and puts them
 * into accessible properties.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CommandLineOptions
{
    /** Error code for invalid input */
    private const INPUT_ERROR = 23;

    /**
     * The minimum rule priority.
     *
     * @var int
     */
    private $minimumPriority = Rule::LOWEST_PRIORITY;

    /**
     * The maximum rule priority.
     *
     * @var int
     */
    private $maximumPriority = Rule::HIGHEST_PRIORITY;

    /**
     * A php source code filename or directory.
     *
     * @var string
     */
    private $inputPath;

    /**
     * The specified report format.
     *
     * @var ?class-string<AbstractRenderer>
     */
    private $reportFormat;

    /** An optional filename for the generated report. */
    private ?string $reportFile = null;

    /** An optional filename to collect errors. */
    private ?string $errorFile = null;

    /**
     * Additional report files.
     *
     * @var array<string, string>
     */
    private $reportFiles = [];

    /**
     * List of deprecations.
     *
     * @var list<string>
     */
    private $deprecations = [];

    /**
     * A ruleset filename or a comma-separated string of ruleset filenames.
     *
     * @var string
     */
    private $ruleSets;

    /** File name of a PHPUnit code coverage report. */
    private ?string $coverageReport = null;

    /** A string of comma-separated extensions for valid php source code filenames. */
    private ?string $extensions = null;

    /**
     * A string of comma-separated pattern that is used to exclude directories.
     *
     * Use asterisks to exclude by pattern. For example *src/foo/*.php or *src/foo/*
     */
    private ?string $ignore = null;

    /**
     * Should the shell show the current phpmd version?
     *
     * @var bool
     */
    private $version = false;

    /**
     * Should PHPMD run in strict mode?
     *
     * @var bool
     * @since 1.2.0
     */
    private $strict = false;

    /** @var int */
    private $verbosity = OutputInterface::VERBOSITY_NORMAL;

    /**
     * Should PHPMD exit without error code even if error is found?
     *
     * @var bool
     * @since 2.10.0
     */
    private $ignoreErrorsOnExit = false;

    /**
     * Should PHPMD exit without error code even if violation is found?
     *
     * @var bool
     */
    private $ignoreViolationsOnExit = false;

    /** Should PHPMD baseline the existing violations and write them to the $baselineFile */
    private BaselineMode $generateBaseline = BaselineMode::None;

    /**
     * The baseline source file to read the baseline violations from.
     * Defaults to the path of the (first) ruleset file as phpmd.baseline.xml
     */
    private ?string $baselineFile = null;

    /**
     * Should PHPMD read or write the result cache state from the cache file
     * @var bool
     */
    private $cacheEnabled = false;

    /**
     * If set the path to read and write the result cache state from and to.
     * @var string|null
     */
    private $cacheFile;

    /** Determine the cache strategy. */
    private ResultCacheStrategy $cacheStrategy = ResultCacheStrategy::Content;

    /**
     * Either the output should be colored.
     *
     * @var bool
     */
    private $colored = false;

    /** Specify how many extra lines are added to a code snippet */
    private ?int $extraLineInExcerpt = null;

    /**
     * Constructs a new command line options instance.
     *
     * @param string[] $args
     * @param array<int, string> $availableRuleSets List of available rule-sets.
     * @throws InvalidArgumentException
     * @throws ValueError
     * @throws TypeError
     */
    public function __construct(
        array $args,
        private readonly array $availableRuleSets = [],
    ) {
        // Remove current file name
        array_shift($args);

        $originalArguments = $args;

        $arguments = [];
        $listenOptions = true;
        $hasImplicitArguments = false;

        while (($arg = array_shift($args)) !== null) {
            if (!$listenOptions) {
                $arguments[] = $arg;

                continue;
            }

            $equalChunk = explode('=', $arg, 2);

            switch ($equalChunk[0]) {
                case '--':
                    $this->refuseValue($equalChunk);
                    $listenOptions = false;

                    break;

                case '--verbose':
                case '-v':
                    $this->refuseValue($equalChunk);
                    $this->verbosity = OutputInterface::VERBOSITY_VERBOSE;

                    break;

                case '-vv':
                    $this->refuseValue($equalChunk);
                    $this->verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE;

                    break;

                case '-vvv':
                    $this->refuseValue($equalChunk);
                    $this->verbosity = OutputInterface::VERBOSITY_DEBUG;

                    break;

                case '--min-priority':
                case '--minimum-priority':
                case '--minimumpriority':
                    $this->minimumPriority = (int) $this->readValue($equalChunk, $args);

                    break;

                case '--max-priority':
                case '--maximum-priority':
                case '--maximumpriority':
                    $this->maximumPriority = (int) $this->readValue($equalChunk, $args);

                    break;

                case '--report-file':
                case '--reportfile':
                    $this->reportFile = (string) $this->readValue($equalChunk, $args);

                    break;

                case '--error-file':
                case '--errorfile':
                    $this->errorFile = (string) $this->readValue($equalChunk, $args);

                    break;

                case '--input-file':
                case '--inputfile':
                    array_unshift($arguments, $this->readInputFile((string) $this->readValue($equalChunk, $args)));

                    break;

                case '--coverage':
                    $this->coverageReport = (string) $this->readValue($equalChunk, $args);

                    break;

                case '--extensions':
                    $this->logDeprecated('extensions', 'suffixes');
                    // Deprecated: We use the suffixes option now
                    $this->extensions = (string) $this->readValue($equalChunk, $args);

                    break;

                case '--suffixes':
                    $this->extensions = (string) $this->readValue($equalChunk, $args);

                    break;

                case '--ignore':
                    $this->logDeprecated('ignore', 'exclude');
                    // Deprecated: We use the exclude option now
                    $this->ignore = (string) $this->readValue($equalChunk, $args);

                    break;

                case '--exclude':
                    $this->ignore = (string) $this->readValue($equalChunk, $args);

                    break;

                case '--color':
                    $this->refuseValue($equalChunk);
                    $this->colored = true;

                    break;

                case '--version':
                    $this->refuseValue($equalChunk);
                    $this->version = true;

                    return;

                case '--strict':
                    $this->refuseValue($equalChunk);
                    $this->strict = true;

                    break;

                case '--not-strict':
                    $this->refuseValue($equalChunk);
                    $this->strict = false;

                    break;

                case '--generate-baseline':
                    $this->refuseValue($equalChunk);
                    $this->generateBaseline = BaselineMode::Generate;

                    break;

                case '--update-baseline':
                    $this->refuseValue($equalChunk);
                    $this->generateBaseline = BaselineMode::Update;

                    break;

                case '--baseline-file':
                    $this->baselineFile = $this->readValue($equalChunk, $args);

                    break;

                case '--cache':
                    $this->refuseValue($equalChunk);
                    $this->cacheEnabled = true;

                    break;

                case '--cache-file':
                    $this->cacheFile = $this->readValue($equalChunk, $args);

                    break;

                case '--cache-strategy':
                    $strategy = (string) $this->readValue($equalChunk, $args);
                    $this->cacheStrategy = ResultCacheStrategy::from($strategy);

                    break;

                case '--ignore-errors-on-exit':
                    $this->refuseValue($equalChunk);
                    $this->ignoreErrorsOnExit = true;

                    break;

                case '--ignore-violations-on-exit':
                    $this->refuseValue($equalChunk);
                    $this->ignoreViolationsOnExit = true;

                    break;

                case '--reportfile-checkstyle':
                case '--reportfile-github':
                case '--reportfile-gitlab':
                case '--reportfile-html':
                case '--reportfile-json':
                case '--reportfile-sarif':
                case '--reportfile-text':
                case '--reportfile-xml':
                    preg_match('(^\-\-reportfile\-(checkstyle|github|gitlab|html|json|sarif|text|xml)$)', $arg, $match);
                    $this->reportFiles[$match[1]] = (string) $this->readValue($equalChunk, $args);

                    break;

                case '--extra-line-in-excerpt':
                    $this->extraLineInExcerpt = (int) $this->readValue($equalChunk, $args);

                    break;

                default:
                    $hasImplicitArguments = true;
                    $arguments[] = $arg;

                    break;
            }
        }

        if (count($arguments) < 3) {
            throw new InvalidArgumentException($this->usage(), self::INPUT_ERROR);
        }

        $validator = new ArgumentsValidator($hasImplicitArguments, $originalArguments, $arguments);

        $this->ruleSets = (string) array_pop($arguments);
        $validator->validate('ruleset', $this->ruleSets);

        $this->reportFormat = array_pop($arguments);
        $validator->validate('report format', $this->reportFormat);

        $this->inputPath = implode(',', $arguments);

        if ($this->inputPath === '-') {
            $this->inputPath = 'php://stdin';

            return;
        }

        foreach ($arguments as $arg) {
            $validator->validate('input path', $arg);
        }
    }

    /**
     * Returns a php source code filename or directory.
     */
    public function getInputPath(): string
    {
        return $this->inputPath;
    }

    /**
     * Returns the specified report format.
     *
     * @return class-string<AbstractRenderer>|null
     */
    public function getReportFormat()
    {
        return $this->reportFormat;
    }

    /**
     * Returns the output filename for a generated report or <b>null</b> when
     * the report should be displayed in STDOUT.
     */
    public function getReportFile(): ?string
    {
        return $this->reportFile;
    }

    /**
     * Returns the output filename for the errors or <b>null</b> when
     * the report should be displayed in STDERR.
     */
    public function getErrorFile(): ?string
    {
        return $this->errorFile;
    }

    /**
     * Return the list of deprecations raised when parsing options.
     *
     * @return list<string>
     */
    public function getDeprecations(): array
    {
        return $this->deprecations;
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
     * Returns a ruleset filename or a comma-separated string of ruleset
     */
    public function getRuleSets(): string
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
     * Returns a string of comma-separated extensions for valid php source code
     * filenames or <b>null</b> when this argument was not set.
     */
    public function getExtensions(): ?string
    {
        return $this->extensions;
    }

    /**
     * Returns string of comma-separated pattern that is used to exclude
     * directories or <b>null</b> when this argument was not set.
     */
    public function getIgnore(): ?string
    {
        return $this->ignore;
    }

    /**
     * Was the <b>--version</b> passed to PHPMD's command line interface?
     */
    public function hasVersion(): bool
    {
        return $this->version;
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

    public function getVerbosity(): int
    {
        return $this->verbosity;
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
        return $this->cacheFile ?? '.phpmd.result-cache.php';
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
    public function extraLineInExcerpt(): ?int
    {
        return $this->extraLineInExcerpt;
    }

    /**
     * Creates a report renderer instance based on the user's command line
     * argument.
     *
     * Valid renderers are:
     * <ul>
     *   <li>xml</li>
     *   <li>html</li>
     *   <li>text</li>
     *   <li>json</li>
     * </ul>
     *
     * @throws InvalidArgumentException When the specified renderer does not exist.
     */
    public function createRenderer(?string $reportFormat = null): AbstractRenderer
    {
        $renderer = $this->createRendererWithoutOptions($reportFormat);

        if ($renderer instanceof Verbose) {
            $renderer->setVerbosityLevel($this->verbosity);
        }

        if ($renderer instanceof Color) {
            $renderer->setColored($this->colored);
        }

        return $renderer;
    }

    /**
     * @throws InvalidArgumentException When the specified renderer does not exist.
     */
    private function createRendererWithoutOptions(?string $reportFormat = null): AbstractRenderer
    {
        $reportFormat = $reportFormat ?: $this->reportFormat;

        return match ($reportFormat) {
            'ansi' => $this->createAnsiRenderer(),
            'checkstyle' => $this->createCheckStyleRenderer(),
            'gitlab' => $this->createGitLabRenderer(),
            'github' => $this->createGitHubRenderer(),
            'html' => $this->createHtmlRenderer(),
            'json' => $this->createJsonRenderer(),
            'sarif' => $this->createSarifRenderer(),
            'text' => $this->createTextRenderer(),
            'xml' => $this->createXmlRenderer(),
            default => $this->createCustomRenderer(),
        };
    }

    private function createXmlRenderer(): XMLRenderer
    {
        return new XMLRenderer();
    }

    private function createTextRenderer(): TextRenderer
    {
        return new TextRenderer();
    }

    private function createAnsiRenderer(): AnsiRenderer
    {
        return new AnsiRenderer();
    }

    private function createGitLabRenderer(): GitLabRenderer
    {
        return new GitLabRenderer();
    }

    private function createGitHubRenderer(): GitHubRenderer
    {
        return new GitHubRenderer();
    }

    private function createHtmlRenderer(): HTMLRenderer
    {
        return new HTMLRenderer($this->extraLineInExcerpt);
    }

    private function createJsonRenderer(): JSONRenderer
    {
        return new JSONRenderer();
    }

    private function createCheckStyleRenderer(): CheckStyleRenderer
    {
        return new CheckStyleRenderer();
    }

    private function createSarifRenderer(): SARIFRenderer
    {
        return new SARIFRenderer();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createCustomRenderer(): AbstractRenderer
    {
        if (!$this->reportFormat) {
            throw new InvalidArgumentException(
                'Can\'t create report with empty format.',
                self::INPUT_ERROR
            );
        }

        if (class_exists($this->reportFormat)) {
            return new $this->reportFormat();
        }

        // Try to load a custom renderer
        $fileName = strtr($this->reportFormat, '_\\', '//') . '.php';

        $fileHandle = @fopen($fileName, 'rb', true);
        if (!is_resource($fileHandle)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Can\'t find the custom report class: %s',
                    $this->reportFormat
                ),
                self::INPUT_ERROR
            );
        }
        @fclose($fileHandle);

        include_once $fileName;

        return new $this->reportFormat();
    }

    /**
     * Returns usage information for the PHPMD command line interface.
     *
     * @throws InvalidArgumentException
     */
    public function usage(): string
    {
        $availableRenderers = $this->getListOfAvailableRenderers();
        $noRenderers = ($availableRenderers === null);

        return 'Mandatory arguments:' . \PHP_EOL .
            '1) A php source code filename or directory. Can be a comma-' .
            'separated string, glob pattern, or "-" to scan stdin' . \PHP_EOL .
            '2) A report format' . \PHP_EOL .
            '3) A ruleset filename or a comma-separated string of ruleset' .
            'filenames' . \PHP_EOL . \PHP_EOL .
            'Example: phpmd /path/to/source format ruleset' . \PHP_EOL . \PHP_EOL .
            ($noRenderers ? 'No available formats' : 'Available formats: ' . $availableRenderers) . '.' . \PHP_EOL .
            'Available rulesets: ' . implode(', ', $this->availableRuleSets) . '.' . \PHP_EOL . \PHP_EOL .
            'Optional arguments that may be put after the mandatory arguments:' .
            \PHP_EOL .
            '--verbose, -v, -vv, -vvv: Show debug information.' . \PHP_EOL .
            '--minimum-priority: rule priority threshold; rules with lower ' .
            'priority than this will not be used' . \PHP_EOL .
            '--report-file: send report output to a file; default to STDOUT' .
            \PHP_EOL .
            '--error-file: send errors (other than reported violations) ' .
            'output to a file; default to STDERR' .
            \PHP_EOL .
            '--suffixes: comma-separated string of valid source code ' .
            'filename extensions, e.g. php,phtml' . \PHP_EOL .
            '--exclude: comma-separated string of patterns that are used to ' .
            'ignore directories. Use asterisks to exclude by pattern. ' .
            'For example *src/foo/*.php or *src/foo/*' . \PHP_EOL .
            '--strict: also report those nodes with a @SuppressWarnings ' .
            'annotation' . \PHP_EOL .
            '--ignore-errors-on-exit: will exit with a zero code, ' .
            'even on error' . \PHP_EOL .
            '--ignore-violations-on-exit: will exit with a zero code, ' .
            'even if any violations are found' . \PHP_EOL .
            '--cache: will enable the result cache.' . \PHP_EOL .
            '--cache-file: instead of the default .phpmd.result-cache.php' .
            ' will use this file as result cache file path.' . \PHP_EOL .
            '--cache-strategy: sets the caching strategy to determine if' .
            ' a file is still fresh. Either `content` to base it on the ' .
            'file contents, or `timestamp` to base it on the file modified ' .
            'timestamp' . \PHP_EOL .
            '--generate-baseline: will generate a phpmd.baseline.xml next ' .
            'to the first ruleset file location' . \PHP_EOL .
            '--update-baseline: will remove any non-existing violations from the phpmd.baseline.xml' . \PHP_EOL .
            '--baseline-file: a custom location of the baseline file' . \PHP_EOL .
            '--color: enable color in output' . \PHP_EOL .
            '--extra-line-in-excerpt: Specify how many extra lines are added ' .
            'to a code snippet in html format' . \PHP_EOL .
            '--: Explicit argument separator: Anything after "--" will be read as an argument even if' .
            'it starts with "-" or matches the name of an option' . \PHP_EOL;
    }

    /**
     * Get a list of available renderers
     *
     * @return string|null The list of renderers found separated by comma, or null if none.
     * @throws InvalidArgumentException
     */
    private function getListOfAvailableRenderers(): ?string
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

        return implode(', ', $renderers) ?: null;
    }

    /**
     * Logs a deprecated option to the current user interface.
     */
    private function logDeprecated(string $deprecatedName, string $newName): void
    {
        $this->deprecations[] = sprintf(
            'The --%s option is deprecated, please use --%s instead.',
            $deprecatedName,
            $newName
        );
    }

    /**
     * This method takes the given input file, reads the newline separated paths
     * from that file and creates a comma separated string of the file paths. If
     * the given <b>$inputFile</b> not exists, this method will throw an
     * exception.
     *
     * @param string $inputFile Specified input file name.
     * @throws InvalidArgumentException If the specified input file does not exist.
     * @since 1.1.0
     */
    private function readInputFile(string $inputFile): string
    {
        $content = @file($inputFile);
        if ($content === false) {
            throw new InvalidArgumentException("Unable to load '{$inputFile}'.");
        }

        return implode(',', array_map(trim(...), $content));
    }

    /**
     * Throw an exception if a boolean option has a value (is followed by equal).
     *
     * @param string[] $equalChunk The CLI parameter split in 2 by "=" sign
     *
     * @throws InvalidArgumentException if a boolean option has a value (is followed by equal)
     */
    private function refuseValue(array $equalChunk): void
    {
        if (count($equalChunk) > 1) {
            throw new InvalidArgumentException($equalChunk[0] . ' option does not accept a value');
        }
    }

    /**
     * Return value for an option either what is after "=" sign if present, else take the next CLI parameter.
     *
     * @param list<string> $equalChunk The CLI parameter split in 2 by "=" sign
     * @param string[] &$args      The remaining CLI parameters not yet parsed
     */
    private function readValue(array $equalChunk, array &$args): ?string
    {
        if (count($equalChunk) > 1) {
            return $equalChunk[1];
        }

        return array_shift($args);
    }
}
