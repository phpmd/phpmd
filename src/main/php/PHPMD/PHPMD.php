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

namespace PHPMD;

use Exception;
use PHPMD\Cache\ResultCacheEngine;

/**
 * This is the main facade of the PHP PMD application
 */
class PHPMD
{
    /** The current PHPMD version. */
    final public const VERSION = '@package_version@';

    /**
     * This property will be set to <b>true</b> when an error
     * was found in the processed source code.
     *
     * @var bool
     * @since 2.10.0
     */
    private $errors = false;

    /**
     * List of valid file extensions for analyzed files.
     *
     * @var list<string>
     */
    private $fileExtensions = ['php', 'php3', 'php4', 'php5', 'inc'];

    /**
     * List of exclude directory patterns.
     *
     * @var list<string>
     */
    private $ignorePatterns = ['.git', '.svn', 'CVS', '.bzr', '.hg', 'SCCS'];

    /**
     * The input source file or directory.
     *
     * @var string
     */
    private $input;

    /** @var ResultCacheEngine|null */
    private $resultCache;

    /**
     * This property will be set to <b>true</b> when a violation
     * was found in the processed source code.
     *
     * @var bool
     * @since 0.2.5
     */
    private $violations = false;

    /**
     * Additional options for PHPMD or one of it's parser backends.
     *
     * @var array<string, string>
     * @since 1.2.0
     */
    private $options = [];

    /**
     * This method will return <b>true</b> when the processed source code
     * contains errors.
     *
     * @return bool
     * @since 2.10.0
     */
    public function hasErrors()
    {
        return $this->errors;
    }

    /**
     * This method will return <b>true</b> when the processed source code
     * contains violations.
     *
     * @return bool
     * @since 0.2.5
     */
    public function hasViolations()
    {
        return $this->violations;
    }

    /**
     * Returns the input source file or directory path.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Returns an array with valid php source file extensions.
     *
     * @return string[]
     * @since 0.2.0
     */
    public function getFileExtensions()
    {
        return $this->fileExtensions;
    }

    /**
     * Sets a list of filename extensions for valid php source code files.
     *
     * @param array<string> $fileExtensions Extensions without leading dot.
     */
    public function setFileExtensions(array $fileExtensions): void
    {
        $this->fileExtensions = $fileExtensions;
    }

    /**
     * Returns an array with string patterns that mark a file path invalid.
     *
     * @return string[]
     * @since 2.9.0
     */
    public function getIgnorePatterns()
    {
        return $this->ignorePatterns;
    }

    /**
     * Add a list of ignore patterns which is used to exclude directories from
     * the source analysis.
     *
     * @param array<string> $ignorePatterns List of ignore patterns.
     * @return $this
     * @since 2.9.0
     */
    public function addIgnorePatterns(array $ignorePatterns)
    {
        $this->ignorePatterns = [
            ...$this->ignorePatterns,
            ...$ignorePatterns,
        ];

        return $this;
    }

    /**
     * @return ResultCacheEngine|null
     */
    public function getResultCache()
    {
        return $this->resultCache;
    }

    /**
     * @param ResultCacheEngine $resultCache
     * @return $this;
     */
    public function setResultCache($resultCache)
    {
        $this->resultCache = $resultCache;

        return $this;
    }

    /**
     * Returns additional options for PHPMD or one of it's parser backends.
     *
     * @return array<string, string>
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets additional options for PHPMD or one of it's parser backends.
     *
     * @param array<string, string> $options Additional backend or PHPMD options.
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * This method will process all files that can be found in the given input
     * path. It will apply rules defined in the comma-separated <b>$ruleSets</b>
     * argument. The result will be passed to all given renderer instances.
     *
     * @param string             $inputPath
     * @param string[]      $ignorePattern
     * @param AbstractRenderer[] $renderers
     * @param RuleSet[]          $ruleSetList
     * @throws Exception
     */
    public function processFiles(
        $inputPath,
        $ignorePattern,
        array $renderers,
        array $ruleSetList,
        Report $report
    ): void {
        // Merge parsed excludes
        $this->addIgnorePatterns($ignorePattern);

        $this->input = $inputPath;

        $factory = new ParserFactory();
        $parser = $factory->create($this);

        foreach ($ruleSetList as $ruleSet) {
            $parser->addRuleSet($ruleSet);
        }

        $report->start();
        $parser->parse($report);
        if ($this->resultCache !== null) {
            $state = $this->resultCache->getFileFilter()->getState();
            $state = $this->resultCache->getUpdater()->update($ruleSetList, $state, $report);
            $this->resultCache->getWriter()->write($state);
        }
        $report->end();

        foreach ($renderers as $renderer) {
            $renderer->start();
        }

        foreach ($renderers as $renderer) {
            $renderer->renderReport($report);
        }

        foreach ($renderers as $renderer) {
            $renderer->end();
        }

        $this->errors = $report->hasErrors();
        $this->violations = !$report->isEmpty();
    }
}
