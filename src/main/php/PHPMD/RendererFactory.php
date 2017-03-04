<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2017, Manuel Pichler <mapi@phpmd.org>.
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
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD;

use PHPMD\Renderer\HTMLRenderer;
use PHPMD\Renderer\TextRenderer;
use PHPMD\Renderer\XMLRenderer;

/**
 * Factory responsible for creating PHPMD rendering engines.
 *
 * @author Prytoegrian <prytoegrian@protonmail.com>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @see ::createRenderer For creating Renderers.
 */
abstract class RendererFactory
{
    /**
     * Creates a report renderer instance based on the user's command line
     * argument.
     *
     * @param AbstractWriter $writer Associated output writer instance
     * @param string $reportFormat Format within xml, html, text or a custom one
     * @return \PHPMD\AbstractRenderer
     */
    public static function createRenderer(AbstractWriter $writer, $reportFormat)
    {
        switch ($reportFormat) {
            case 'xml':
                return new XMLRenderer($writer);
            case 'html':
                return new HTMLRenderer($writer);
            case 'text':
                return new TextRenderer($writer);
            default:
                return static::createCustomRenderer($writer, $reportFormat);
        }
    }

    /**
     * Create a custom renderer, for user's specific needs
     *
     * @param AbstractWriter $writer Associated output writer instance
     * @param string $reportFormat A custom format, created by the user (for instance, json)
     * @return \PHPMD\AbstractRenderer
     * @throws \InvalidArgumentException When the format is empty
     */
    private static function createCustomRenderer(AbstractWriter $writer, $reportFormat)
    {
        if ($reportFormat === '') {
            throw new \InvalidArgumentException(
                'Can\'t create report with empty format.'
            );
        }
        if (!class_exists($reportFormat)) {
            static::loadCustomRendererClassFile($reportFormat);
        }

        return new $reportFormat($writer);
    }

    /**
     * Try to load a custom renderer class file
     *
     * @param string $reportFormat A custom format, created by the user (for instance, json)
     * @throws \InvalidArgumentException When there's no file loadable
     */
    private static function loadCustomRendererClassFile($reportFormat)
    {
        $fileName = strtr($reportFormat, '_\\', '//') . '.php';
        if (!file_exists($fileName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Can\'t find the custom report class: %s',
                    $reportFormat
                )
            );
        }

        include_once $fileName;
    }
}
