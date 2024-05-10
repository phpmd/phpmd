<?php

namespace PHPMD\Renderer;

use PHPMD\Writer\StreamWriter;

class RendererFactory
{
    /**
     * @return BaselineRenderer
     */
    public static function createBaselineRenderer(StreamWriter $writer)
    {
        // set base path to current working directory
        $renderer = new BaselineRenderer(getcwd() ?: '');
        $renderer->setWriter($writer);

        return $renderer;
    }
}
