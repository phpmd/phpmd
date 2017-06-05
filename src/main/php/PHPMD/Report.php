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
 * The report class collects all found violations and further information about
 * a PHPMD run.
 */
class Report
{
    /**
     * List of rule violations detected in the analyzed source code.
     *
     * @var array
     */
    private $ruleViolations = array();

    /**
     * The start time for this report.
     *
     * @var float
     */
    private $startTime = 0.0;

    /**
     * The end time for this report.
     *
     * @var float
     */
    private $endTime = 0.0;

    /**
     * Errors that occurred while parsing the source.
     *
     * @var array
     * @since 1.2.1
     */
    private $errors = array();

    /**
     * Adds a rule violation to this report.
     *
     * @param \PHPMD\RuleViolation $violation
     * @return void
     */
    public function addRuleViolation(RuleViolation $violation)
    {
        $fileName = $violation->getFileName();
        if (!isset($this->ruleViolations[$fileName])) {
            $this->ruleViolations[$fileName] = array();
        }

        $beginLine = $violation->getBeginLine();
        if (!isset($this->ruleViolations[$fileName][$beginLine])) {
            $this->ruleViolations[$fileName][$beginLine] = array();
        }

        $this->ruleViolations[$fileName][$beginLine][] = $violation;
    }

    /**
     * Returns <b>true</b> when this report does not contain any errors.
     *
     * @return boolean
     * @since 0.2.5
     */
    public function isEmpty()
    {
        return (count($this->ruleViolations) === 0);
    }

    /**
     * Returns an iterator with all occurred rule violations.
     *
     * @return \PHPMD\RuleViolation[]
     */
    public function getRuleViolations()
    {
        // First sort by file name
        ksort($this->ruleViolations);

        $violations = array();
        foreach ($this->ruleViolations as $violationInLine) {
            // Second sort is by line number
            ksort($violationInLine);

            foreach ($violationInLine as $violation) {
                $violations = array_merge($violations, $violation);
            }
        }

        return new \ArrayIterator($violations);
    }

    /**
     * Adds a processing error that occurred while parsing the source.
     *
     * @param \PHPMD\ProcessingError $error
     * @return void
     * @since 1.2.1
     */
    public function addError(ProcessingError $error)
    {
        $this->errors[] = $error;
    }

    /**
     * Returns <b>true</b> when the report contains at least one processing
     * error. Otherwise this method will return <b>false</b>.
     *
     * @return boolean
     * @since 1.2.1
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Returns an iterator with all {@link \PHPMD\ProcessingError} that were
     * added to this report.
     *
     * @return \Iterator
     * @since 1.2.1
     */
    public function getErrors()
    {
        return new \ArrayIterator($this->errors);
    }

    /**
     * Starts the time tracking of this report instance.
     *
     * @return void
     */
    public function start()
    {
        $this->startTime = microtime(true) * 1000.0;
    }

    /**
     * Stops the time tracking of this report instance.
     *
     * @return void
     */
    public function end()
    {
        $this->endTime = microtime(true) * 1000.0;
    }

    /**
     * Returns the total time elapsed for the source analysis.
     *
     * @return float
     */
    public function getElapsedTimeInMillis()
    {
        return round($this->endTime - $this->startTime);
    }
}
