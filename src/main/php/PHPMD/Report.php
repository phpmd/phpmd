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

use ArrayIterator;
use PHPMD\Baseline\BaselineValidator;

/**
 * The report class collects all found violations and further information about
 * a PHPMD run.
 */
class Report
{
    /**
     * List of rule violations detected in the analyzed source code.
     *
     * @var array<string, array<int, list<RuleViolation>>>
     */
    private array $ruleViolations = [];

    /** The start time for this report. */
    private float $startTime = 0.0;

    /** The end time for this report. */
    private float $endTime = 0.0;

    /**
     * Errors that occurred while parsing the source.
     *
     * @var list<ProcessingError>
     * @since 1.2.1
     */
    private array $errors = [];

    public function __construct(
        private readonly ?BaselineValidator $baselineValidator = null,
    ) {
    }

    /**
     * Adds a rule violation to this report.
     */
    public function addRuleViolation(RuleViolation $violation): void
    {
        if ($this->baselineValidator !== null && $this->baselineValidator->isBaselined($violation)) {
            return;
        }

        $fileName = $violation->getFileName();
        if (!isset($this->ruleViolations[$fileName])) {
            $this->ruleViolations[$fileName] = [];
        }

        $beginLine = $violation->getBeginLine();
        if (!isset($this->ruleViolations[$fileName][$beginLine])) {
            $this->ruleViolations[$fileName][$beginLine] = [];
        }

        $this->ruleViolations[$fileName][$beginLine][] = $violation;
    }

    /**
     * Returns <b>true</b> when this report does not contain any errors.
     *
     * @since 0.2.5
     */
    public function isEmpty(): bool
    {
        return (count($this->ruleViolations) === 0);
    }

    /**
     * Returns an iterator with all occurred rule violations.
     *
     * @return ArrayIterator<int, RuleViolation>
     */
    public function getRuleViolations(): ArrayIterator
    {
        // First sort by file name
        ksort($this->ruleViolations);

        $violations = [];
        foreach ($this->ruleViolations as $violationInLine) {
            // Second sort is by line number
            ksort($violationInLine);

            foreach ($violationInLine as $violation) {
                $violations = [...$violations, ...$violation];
            }
        }

        return new ArrayIterator($violations);
    }

    /**
     * Adds a processing error that occurred while parsing the source.
     *
     * @since 1.2.1
     */
    public function addError(ProcessingError $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * Returns <b>true</b> when the report contains at least one processing
     * error. Otherwise this method will return <b>false</b>.
     *
     * @since 1.2.1
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * Returns an iterator with all {@link \PHPMD\ProcessingError} that were
     * added to this report.
     *
     * @return ArrayIterator<int, ProcessingError>
     * @since 1.2.1
     */
    public function getErrors(): ArrayIterator
    {
        return new ArrayIterator($this->errors);
    }

    /**
     * Starts the time tracking of this report instance.
     */
    public function start(): void
    {
        $this->startTime = microtime(true) * 1000.0;
    }

    /**
     * Stops the time tracking of this report instance.
     */
    public function end(): void
    {
        $this->endTime = microtime(true) * 1000.0;
    }

    /**
     * Returns the total time elapsed for the source analysis.
     */
    public function getElapsedTimeInMillis(): float
    {
        return round($this->endTime - $this->startTime);
    }
}
