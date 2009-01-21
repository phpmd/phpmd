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
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/pmd
 */

require_once 'PHP/PMD.php';
require_once 'PHP/PMD/TextUI/CommandLineOptions.php';

/**
 * This class provides a command line interface for PHP_PMD
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/pmd
 */
class PHP_PMD_TextUI_Command
{
    /**
     * This method creates a PHP_PMD instance and configures this object based
     * on the user's input, then it starts the source analysis.
     *
     * @param PHP_PMD_TextUI_CommandLineOptions $opts The prepared command line
     *                                                arguments.
     *
     * @return void
     */
    public function run(PHP_PMD_TextUI_CommandLineOptions $opts)
    {
        // Create a report stream
        if ($opts->getReportFile() === null) {
            $stream = STDOUT;
        } else {
            $stream = fopen($opts->getReportFile(), 'wb');
        }

        // Create renderer and configure output
        $renderer = $opts->createRenderer();
        $renderer->setWriter(new PHP_PMD_Writer_Stream($stream));

        // Create a rule set factory
        $ruleSetFactory = new PHP_PMD_RuleSetFactory();
        $ruleSetFactory->setMinimumPriority($opts->getMinimumPriority());

        $phpmd = new PHP_PMD();

        $extensions = $opts->getExtensions();
        if ($extensions !== null) {
            $phpmd->setFileExtensions(explode(',', $extensions));
        }

        $ignore = $opts->getIgnore();
        if ($ignore !== null) {
            $phpmd->setIgnorePattern(explode(',', $ignore));
        }

        $phpmd->processFiles($opts->getInputPath(),
                             $opts->getRuleSets(),
                             array($renderer),
                             $ruleSetFactory);
    }

    /**
     * The main method that can be used by a calling shell script, the return
     * value can be used as exit code.
     *
     * @param array $args The raw command line arguments array.
     *
     * @return integer
     */
    public static function main(array $args)
    {
        try {
            $opts = new PHP_PMD_TextUI_CommandLineOptions($args);

            $cmd = new PHP_PMD_TextUI_Command();
            $cmd->run($opts);
        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
            return $e->getCode();
        }
        return 0;
    }
}
?>
