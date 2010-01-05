<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
<<<<<<< HEAD
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@pdepend.org>.
=======
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@phpmd.org>.
>>>>>>> 0.2.x
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
<<<<<<< HEAD
 * @author     Manuel Pichler <mapi@pdepend.org>
=======
 * @author     Manuel Pichler <mapi@phpmd.org>
>>>>>>> 0.2.x
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once 'PHP/PMD/AbstractRule.php';

/**
 * This is a helper class that collects the specified cli arguments and puts them
 * into accessible properties.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage TextUI
<<<<<<< HEAD
 * @author     Manuel Pichler <mapi@pdepend.org>
=======
 * @author     Manuel Pichler <mapi@phpmd.org>
>>>>>>> 0.2.x
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_TextUI_CommandLineOptions
{
    /**
     * Error code for invalid input
     */
    const INPUT_ERROR = 23;

    /**
     * The minimum rule priority.
     *
     * @var integer $_minimumPriority
     */
    private $_minimumPriority = PHP_PMD_AbstractRule::LOWEST_PRIORITY;

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
     * An optional filename for the generated report.
     *
     * @var string $_reportFile
     */
    private $_reportFile = null;

    /**
     * A ruleset filename or a comma-separated string of ruleset filenames.
     *
     * @var string $_ruleSets
     */
    private $_ruleSets = null;

    /**
     * A string of comma-separated extensions for valid php source code filenames.
     *
     * @var string $_extensions
     */
    private $_extensions = null;

    /**
     * A string of comma-separated pattern that is used to exclude directories.
     *
     * @var string $_ignore
     */
    private $_ignore = null;
    
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
            throw new InvalidArgumentException($this->usage(), self::INPUT_ERROR);
        }

        $this->_inputPath    = array_shift($args);
        $this->_reportFormat = array_shift($args);
        $this->_ruleSets     = array_shift($args);

        while (($arg = array_shift($args)) !== null) {
            switch ($arg) {

            case '--minimumpriority':
                $this->_minimumPriority = (int) array_shift($args);
                break;

            case '--reportfile':
                $this->_reportFile = array_shift($args);
                break;

            case '--extensions':
                $this->_extensions = array_shift($args);
                break;

            case '--ignore':
                $this->_ignore = array_shift($args);
                break;
            }
        }
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

    /**
     * Returns the output filename for a generated report or <b>null</b> when
     * the report should be displayed in STDOUT.
     *
     * @return string
     */
    public function getReportFile()
    {
        return $this->_reportFile;
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

    /**
     * Returns the minimum rule priority.
     *
     * @return integer
     */
    public function getMinimumPriority()
    {
        return $this->_minimumPriority;
    }

    /**
     * Returns a string of comma-separated extensions for valid php source code
     * filenames or <b>null</b> when this argument was not set.
     *
     * @return string
     */
    public function getExtensions()
    {
        return $this->_extensions;
    }

    /**
     * Returns  string of comma-separated pattern that is used to exclude
     * directories or <b>null</b> when this argument was not set.
     *
     * @return string
     */
    public function getIgnore()
    {
        return $this->_ignore;
    }

    /**
     * Creates a report renderer instance based on the user's command line
     * argument.
     *
     * Valid renderers are:
     * <ul>
     *   <li>xml</li>
     * </ul>
     *
     * @return PHP_PMD_AbstractRenderer
     * @throws InvalidArgumentException When the specified renderer does not
     *                                  exist.
     */
    public function createRenderer()
    {
        switch ($this->_reportFormat) {

        case 'xml':
            include_once 'PHP/PMD/Renderer/XMLRenderer.php';
            return new PHP_PMD_Renderer_XMLRenderer();

        case 'html':
            include_once 'PHP/PMD/Renderer/HTMLRenderer.php';
            return new PHP_PMD_Renderer_HTMLRenderer();

        case 'text':
            include_once 'PHP/PMD/Renderer/TextRenderer.php';
            return new PHP_PMD_Renderer_TextRenderer();

        default:
            if ($this->_reportFormat !== '') {

                // Try to load a custom renderer
                $fileName = strtr($this->_reportFormat, '_', '/') . '.php';

                $fp = @fopen($fileName, 'r', true);
                if (is_resource($fp) === false) {
                    $message = 'Can\'t find the custom report class: '
                             . $this->_reportFormat;
                    throw new InvalidArgumentException($message, self::INPUT_ERROR);
                }
                @fclose($fp);

                include_once $fileName;

                return new $this->_reportFormat;
            }
            $message = 'Can\'t create report with format of ' . $this->_reportFormat;
            throw new InvalidArgumentException($message, self::INPUT_ERROR);
        }
    }

    /**
     * Returns usage information for the PHP_PMD command line interface.
     *
     * @return string
     */
    public function usage()
    {
        return 'Mandatory arguments:' . PHP_EOL .
               '1) A php source code filename or directory' . PHP_EOL .
               '2) A report format' . PHP_EOL .
               '3) A ruleset filename or a comma-separated string of ruleset' .
               'filenames' . PHP_EOL . PHP_EOL .
               'Optional arguments that may be put after the mandatory arguments:' .
               PHP_EOL .
               '--minimumpriority: rule priority threshold; rules with lower ' .
               'priority than this will not be used' . PHP_EOL .
               '--reportfile: send report output to a file; default to STDOUT' .
               PHP_EOL .
               '--extensions: comma-separated string of valid source code ' .
               'filename extensions' . PHP_EOL .
               '--ignore: comma-separated string of patterns that are used to ' .
               'ignore directories' . PHP_EOL;
    }
}