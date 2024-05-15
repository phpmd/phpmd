<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractWriter;

final class RendererFactory
{
    /**
     * @return BaselineRenderer
     */
    public static function createBaselineRenderer(AbstractWriter $writer)
    {
        // set base path to current working directory
        $renderer = new BaselineRenderer(getcwd() ?: '');
        $renderer->setWriter($writer);

        return $renderer;
    }
}
