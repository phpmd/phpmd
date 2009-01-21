<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/php-pmd
 */

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Input/ExcludePathFilter.php';
require_once 'PHP/Depend/Input/ExtensionFilter.php';

require_once 'PHP/PMD/Report.php';
require_once 'PHP/PMD/RuleSetFactory.php';
require_once 'PHP/PMD/Adapter/Metrics.php';
require_once 'PHP/PMD/Writer/Stream.php';

/**
 * This is the main facade of the PHP PMD application
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/php-pmd
 */
final class PHP_PMD
{
    /**
     * The current PHP_PMD version.
     */
    const VERSION = '@package_version@';

    /**
     * List of valid file extensions for analyzed files.
     *
     * @var array(string) $_fileExtensions
     */
    private $_fileExtensions = array('php', 'php3', 'php4', 'php5', 'inc');

    /**
     * List of exclude directory patterns.
     *
     * @var array(string) $_ignorePatterns
     */
    private $_ignorePatterns = array('.git', '.svn', 'CVS');

    /**
     * Sets a list of filename extensions for valid php source code files.
     *
     * @param array(string) $fileExtensions Extensions without leading dot.
     *
     * @return void
     */
    public function setFileExtensions(array $fileExtensions)
    {
        $this->_fileExtensions = $fileExtensions;
    }

    /**
     * Sets a list of ignore patterns that is used to exclude directories from
     * the source analysis.
     *
     * @param array(string) $ignorePatterns List of ignore patterns.
     *
     * @return void
     */
    public function setIgnorePattern(array $ignorePatterns)
    {
        $this->_ignorePatterns = $ignorePatterns;
    }

    /**
     * This method will process all files that can be found in the given input
     * path. It will apply rules defined in the comma-separated <b>$ruleSets</b>
     * argument. The result will be passed to all given renderer instances.
     *
     * @param string                          $inputPath      File or directory
     * @param string                          $ruleSets       Rule-sets to apply
     * @param array(PHP_PMD_AbstractRenderer) $renderers      Report renderers
     * @param PHP_PMD_RuleSetFactory          $ruleSetFactory The factory to use
     *
     * @return void
     */
    public function processFiles($inputPath, 
                                 $ruleSets,
                                 array $renderers,
                                 PHP_PMD_RuleSetFactory $ruleSetFactory)
    {
        $ruleSets = $ruleSetFactory->createRuleSets($ruleSets);

        $report = new PHP_PMD_Report();

        $adapter = new PHP_PMD_Adapter_Metrics();
        $adapter->setReport($report);

        foreach ($ruleSets as $ruleSet) {
            $adapter->addRuleSet($ruleSet);
        }

        $pdepend = $this->_createPhpDepend($inputPath);
        $pdepend->addLogger($adapter);
        $pdepend->setSupportBadDocumentation();
        
        $report->start();
        $pdepend->analyze();
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
    }

    /**
     * Creates the used PHP_Depend analyzer instance.
     *
     * @param string $inputPath The input filename or directory.
     *
     * @return PHP_Depend
     */
    private function _createPhpDepend($inputPath)
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(realpath($inputPath));

        if (count($this->_ignorePatterns) > 0) {
            $filter = new PHP_Depend_Input_ExcludePathFilter($this->_ignorePatterns);
            $pdepend->addFileFilter($filter);
        }

        if (count($this->_fileExtensions) > 0) {
            $filter = new PHP_Depend_Input_ExtensionFilter($this->_fileExtensions);
            $pdepend->addFileFilter($filter);
        }

        return $pdepend;
    }
}
?>
