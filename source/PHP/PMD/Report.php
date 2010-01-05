<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/pmd
 */

/**
 * The report class collects all found violations and further information about
 * a PHP_PMD run.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/pmd
 */
class PHP_PMD_Report
{
    /**
     * List of rule violations detected in the analyzed source code.
     *
     * @var array(PHP_PMD_RuleViolations) $_ruleViolations
     */
    private $_ruleViolations = array();

    /**
     * The start time for this report.
     *
     * @var float $_startTime
     */
    private $_startTime = 0.0;

    /**
     * The end time for this report.
     *
     * @var float $_endTime
     */
    private $_endTime = 0.0;

    /**
     * Adds a rule violation to this report.
     *
     * @param PHP_PMD_RuleViolation $violation The occured rule violation.
     *
     * @return void
     */
    public function addRuleViolation(PHP_PMD_RuleViolation $violation)
    {
        $fileName = $violation->getFileName();
        if (!isset($this->_ruleViolations[$fileName])) {
            $this->_ruleViolations[$fileName] = array();
        }

        $beginLine = $violation->getBeginLine();
        if (!isset($this->_ruleViolations[$fileName][$beginLine])) {
            $this->_ruleViolations[$fileName][$beginLine] = array();
        }

        $this->_ruleViolations[$fileName][$beginLine][] = $violation;
    }

    /**
     * Returns an iterator with all occured rule violations.
     *
     * @return Iterator
     */
    public function getRuleViolations()
    {
        // First sort by file name
        ksort($this->_ruleViolations);

        $violations = array();
        foreach ($this->_ruleViolations as $violationInLine) {
            // Second sort is by line number
            ksort($violationInLine);

            foreach ($violationInLine as $violation) {
                $violations = array_merge($violations, $violation);
            }
        }

        return new ArrayIterator($violations);
    }

    /**
     * Starts the time tracking of this report instance.
     *
     * @return void
     */
    public function start()
    {
        $this->_startTime = microtime(true) * 1000.0;
    }

    /**
     * Stops the time tracking of this report instance.
     *
     * @return void
     */
    public function end()
    {
        $this->_endTime = microtime(true) * 1000.0;
    }

    /**
     * Returns the total time elapsed for the source analysis.
     *
     * @return float
     */
    public function getElapsedTimeInMillis()
    {
        return round($this->_endTime - $this->_startTime);
    }
}