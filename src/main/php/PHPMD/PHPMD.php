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

namespace PHPMD;

/**
 * This is the main facade of the PHP PMD application
 */
class PHPMD
{
    /**
     * The current PHPMD version.
     */
    const VERSION = '@package_version@';

    /**
     * This property will be set to <b>true</b> when an error
     * was found in the processed source code.
     *
     * @var boolean
     * @since 2.10.0
     */
    private $errors = false;

    /**
     * List of valid file extensions for analyzed files.
     *
     * @var array(string)
     */
    private $fileExtensions = array('php', 'php3', 'php4', 'php5', 'inc');

    /**
     * List of exclude directory patterns.
     *
     * @var array(string)
     */
    private $ignorePatterns = array('.git', '.svn', 'CVS', '.bzr', '.hg', 'SCCS');

    /**
     * The input source file or directory.
     *
     * @var string
     */
    private $input;

    /**
     * This property will be set to <b>true</b> when a violation
     * was found in the processed source code.
     *
     * @var boolean
     * @since 0.2.5
     */
    private $violations = false;

    /**
     * Additional options for PHPMD or one of it's parser backends.
     *
     * @var array
     * @since 1.2.0
     */
    private $options = array();

    /**
     * This method will return <b>true</b> when the processed source code
     * contains errors.
     *
     * @return boolean
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
     * @return boolean
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
     * @return void
     */
    public function setFileExtensions(array $fileExtensions)
    {
        $this->fileExtensions = $fileExtensions;
    }

    /**
     * Returns an array with string patterns that mark a file path as invalid.
     *
     * @return string[]
     * @since 0.2.0
     * @deprecated 3.0.0 Use getIgnorePatterns() instead, you always get a list of patterns.
     */
    public function getIgnorePattern()
    {
        return $this->getIgnorePatterns();
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
     * Sets a list of ignore patterns that is used to exclude directories from
     * the source analysis.
     *
     * @param array<string> $ignorePatterns List of ignore patterns.
     * @return void
     * @deprecated 3.0.0 Use addIgnorePatterns() instead, both will add an not set the patterns.
     */
    public function setIgnorePattern(array $ignorePatterns)
    {
        $this->addIgnorePatterns($ignorePatterns);
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
        $this->ignorePatterns = array_merge(
            $this->ignorePatterns,
            $ignorePatterns
        );

        return $this;
    }

    /**
     * Returns additional options for PHPMD or one of it's parser backends.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets additional options for PHPMD or one of it's parser backends.
     *
     * @param array $options Additional backend or PHPMD options.
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * This method will process all files that can be found in the given input
     * path. It will apply rules defined in the comma-separated <b>$ruleSets</b>
     * argument. The result will be passed to all given renderer instances.
     *
     * @param string $inputPath
     * @param string $ruleSets
     * @param \PHPMD\AbstractRenderer[] $renderers
     * @param \PHPMD\RuleSetFactory $ruleSetFactory
     * @param \PHPMD\Report $report
     * @return void
     */
    public function processFiles(
        $inputPath,
        $ruleSets,
        array $renderers,
        RuleSetFactory $ruleSetFactory,
        Report $report
    ) {
        // Merge parsed excludes
        $this->addIgnorePatterns($ruleSetFactory->getIgnorePattern($ruleSets));

        $this->input = $inputPath;

        $factory = new ParserFactory();
        $parser = $factory->create($this);

        foreach ($ruleSetFactory->createRuleSets($ruleSets) as $ruleSet) {
            $parser->addRuleSet($ruleSet);
        }

        $report->start();
        $parser->parse($report);
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
