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

use SebastianBergmann\Version;

/**
 * Abstract base class for PHPMD rendering engines.
 */
abstract class AbstractRenderer
{
    /**
     * The associated output writer instance.
     *
     * @var \PHPMD\AbstractWriter
     */
    private $writer = null;

    /**
     * Returns the associated output writer instance.
     *
     * @return \PHPMD\AbstractWriter
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Returns the associated output writer instance.
     *
     * @param \PHPMD\AbstractWriter $writer
     * @return void
     */
    public function setWriter(AbstractWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * This method will be called on all renderers before the engine starts the
     * real report processing.
     *
     * @return void
     */
    public function start()
    {
        // Just a hook
    }

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     *
     * @param \PHPMD\Report $report
     * @return void
     */
    abstract public function renderReport(Report $report);

    /**
     * This method will be called the engine has finished the report processing
     * for all registered renderers.
     *
     * @return void
     */
    public function end()
    {
        // Just a hook
    }

    /**
     * Returns the current version number.
     *
     * @return string
     */
    public static function getVersion()
    {
        $version = PHPMD::VERSION;

        if ('@package_version@' == $version) {
            $build = __DIR__ . '/../../../../build.properties';
            if (file_exists($build)) {
                $data    = @parse_ini_file($build);
                $version = isset($data['project.version']) ? $data['project.version'] : $version;
            }
        }
        if ('@package_version@' == $version) {
            // on previous attempt,
            // if "build.properties" file does not exist or did not define "project.version" property
            // then fallback to git latest tag

            $gitVersion = new Version('2.x-dev', __DIR__ . '/../../../../');

            if (\method_exists($gitVersion, 'asString')) {
                // compatibility with current version 4.x (or greater)
                $version = $gitVersion->asString();
            } else {
                // make compatibility with previous versions 1.x, 2.x and 3.x
                $version = $gitVersion->getVersion();
            }
        }

        return $version;
    }
}
