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

use Exception;
use PHPMD\Renderer\RendererInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract base class for PHPMD rendering engines.
 */
abstract class AbstractRenderer implements RendererInterface
{
    /** The associated output writer instance. */
    private OutputInterface $writer;

    /**
     * Returns the associated output writer instance.
     */
    public function getWriter(): OutputInterface
    {
        return $this->writer;
    }

    /**
     * Returns the associated output writer instance.
     */
    public function setWriter(OutputInterface $writer): void
    {
        $this->writer = $writer;
    }

    /**
     * This method will be called on all renderers before the engine starts the
     * real report processing.
     */
    public function start(): void
    {
        // Just a hook
    }

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     *
     * @throws Exception
     */
    abstract public function renderReport(Report $report): void;

    /**
     * This method will be called the engine has finished the report processing
     * for all registered renderers.
     */
    public function end(): void
    {
        // Just a hook
    }
}
