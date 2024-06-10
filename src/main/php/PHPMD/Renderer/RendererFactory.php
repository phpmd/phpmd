<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractWriter;

final class RendererFactory
{
    public static function createBaselineRenderer(AbstractWriter $writer): BaselineRenderer
    {
        // set base path to current working directory
        $renderer = new BaselineRenderer(getcwd() ?: '');
        $renderer->setWriter($writer);

        return $renderer;
    }
}
