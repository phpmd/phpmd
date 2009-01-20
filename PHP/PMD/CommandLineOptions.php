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
 * @link      http://www.pdepend.org/pmd
 */

/**
 * 
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/pmd
 */
class PHP_PMD_CommandLineOptions
{
    /**
     * A php source code filename or directory.
     *
     * @var string $_inputPath
     */
    private $_inputPath = null;

    /**
     * The specified report format.
     *
     * @var string $_reportFormat
     */
    private $_reportFormat = null;

    /**
     * A ruleset filename or a comma-separated string of ruleset filenames.
     *
     * @var string $_ruleSets
     */
    private $_ruleSets = null;
    
    /**
     * Constructs a new command line options instance.
     *
     * @param array(string) $args The cli arguments.
     */
    public function __construct(array $args)
    {
        // Remove current file name
        array_shift($args);

        if (count($args) < 3) {
            throw new InvalidArgumentException($this->usage());
        }

        $this->_inputPath    = array_shift($args);
        $this->_reportFormat = array_shift($args);
        $this->_ruleSets     = array_shift($args);
    }

    /**
     * Returns a php source code filename or directory.
     *
     * @return string
     */
    public function getInputPath()
    {
        return $this->_inputPath;
    }

    /**
     * Returns the specified report format.
     *
     * @return string
     */
    public function getReportFormat()
    {
        return $this->_reportFormat;
    }

    public function getReportFile()
    {
        return null;
    }

    /**
     * Returns a ruleset filename or a comma-separated string of ruleset
     *
     * @return string
     */
    public function getRuleSets()
    {
        return $this->_ruleSets;
    }

    public function createRenderer()
    {
        switch ($this->_reportFormat) {

        case 'xml':
            include_once 'PHP/PMD/Renderer/XMLRenderer.php';
            return new PHP_PMD_Renderer_XMLRenderer();

        default:
            if ($this->_reportFormat !== '') {

                // Try to load a custom renderer
                $fileName = strtr($this->_reportFormat, '_', '/') . '.php';

                $fp = @fopen($fileName, 'r', true);
                if (is_resource($fp) === false) {
                    $message = 'Can\'t find the custom report class: '
                             . $this->_reportFormat;
                }
                @fclose($fp);

                include_once $fileName;

                return new $this->_reportFormat;
            }
            $message = 'Can\'t create report with format of ' . $this->_reportFormat;
            throw new InvalidArgumentException($message);
        }
    }

    public function usage()
    {
        return PHP_EOL . PHP_EOL .
               'Mandatory arguments:' . PHP_EOL .
               '1) A php source code filename or directory' . PHP_EOL .
               '2) A report format' . PHP_EOL .
               '3) A ruleset filename or a comma-separated string of ruleset' .
               'filenames' . PHP_EOL . PHP_EOL;
    }
}
?>
