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

use PDepend\Application;
use PDepend\Engine;
use PDepend\Input\ExcludePathFilter;
use PDepend\Input\ExtensionFilter;

/**
 * Simple factory that is used to return a ready to use PDepend instance.
 */
class ParserFactory
{
    /** @var string The default config file name */
    const PDEPEND_CONFIG_FILE_NAME = '/pdepend.xml';

    /** @var string The distribution config file name */
    const PDEPEND_CONFIG_FILE_NAME_DIST = '/pdepend.xml.dist';

    /**
     * Mapping between phpmd option names and those used by pdepend.
     *
     * @var array
     */
    private $phpmd2pdepend = array(
        'coverage' => 'coverage-report',
    );

    /**
     * Creates the used {@link \PHPMD\Parser} analyzer instance.
     *
     * @param \PHPMD\PHPMD $phpmd
     * @return \PHPMD\Parser
     */
    public function create(PHPMD $phpmd)
    {
        $pdepend = $this->createInstance();
        $pdepend = $this->init($pdepend, $phpmd);

        return new Parser($pdepend);
    }

    /**
     * Creates a clean php depend instance with some base settings.
     *
     * @return \PDepend\Engine
     */
    private function createInstance()
    {
        $application = new Application();

        $currentWorkingDirectory = getcwd();
        if (file_exists($currentWorkingDirectory . self::PDEPEND_CONFIG_FILE_NAME)) {
            $application->setConfigurationFile($currentWorkingDirectory . self::PDEPEND_CONFIG_FILE_NAME);
        } elseif (file_exists($currentWorkingDirectory . self::PDEPEND_CONFIG_FILE_NAME_DIST)) {
            $application->setConfigurationFile($currentWorkingDirectory . self::PDEPEND_CONFIG_FILE_NAME_DIST);
        }

        return $application->getEngine();
    }

    /**
     * Configures the given PDepend\Engine instance based on some user settings.
     *
     * @param \PDepend\Engine $pdepend
     * @param \PHPMD\PHPMD $phpmd
     * @return \PDepend\Engine
     */
    private function init(Engine $pdepend, PHPMD $phpmd)
    {
        $this->initOptions($pdepend, $phpmd);
        $this->initInput($pdepend, $phpmd);
        $this->initIgnores($pdepend, $phpmd);
        $this->initExtensions($pdepend, $phpmd);

        return $pdepend;
    }

    /**
     * Configures the input source.
     *
     * @param \PDepend\Engine $pdepend
     * @param \PHPMD\PHPMD $phpmd
     * @return void
     */
    private function initInput(Engine $pdepend, PHPMD $phpmd)
    {
        foreach (explode(',', $phpmd->getInput()) as $path) {
            $trimmedPath = trim($path);
            if (is_dir($trimmedPath)) {
                $pdepend->addDirectory($trimmedPath);
                continue;
            }
            $pdepend->addFile($trimmedPath);
        }
    }

    /**
     * Initializes the ignored files and path's.
     *
     * @param \PDepend\Engine $pdepend
     * @param \PHPMD\PHPMD $phpmd
     * @return void
     */
    private function initIgnores(Engine $pdepend, PHPMD $phpmd)
    {
        if (count($phpmd->getIgnorePattern()) > 0) {
            $pdepend->addFileFilter(
                new ExcludePathFilter($phpmd->getIgnorePattern())
            );
        }
    }

    /**
     * Initializes the accepted php source file extensions.
     *
     * @param \PDepend\Engine $pdepend
     * @param \PHPMD\PHPMD $phpmd
     * @return void
     */
    private function initExtensions(Engine $pdepend, PHPMD $phpmd)
    {
        if (count($phpmd->getFileExtensions()) > 0) {
            $pdepend->addFileFilter(
                new ExtensionFilter($phpmd->getFileExtensions())
            );
        }
    }

    /**
     * Initializes additional options for pdepend.
     *
     * @param \PDepend\Engine $pdepend
     * @param \PHPMD\PHPMD $phpmd
     * @return void
     */
    private function initOptions(Engine $pdepend, PHPMD $phpmd)
    {
        $options = array();
        foreach (array_filter($phpmd->getOptions()) as $name => $value) {
            if (isset($this->phpmd2pdepend[$name])) {
                $options[$this->phpmd2pdepend[$name]] = $value;
            }
        }
        $pdepend->setOptions($options);
    }
}
