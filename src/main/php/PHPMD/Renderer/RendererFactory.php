<?php

namespace PHPMD\Renderer;

use PHPMD\Utility\Paths;
use PHPMD\Writer\StreamWriter;
use RuntimeException;

class RendererFactory
{
    /**
     * @return BaselineRenderer
     * @throws RuntimeException
     */
    public static function createBaselineRenderer(StreamWriter $writer)
    {
        // determine basedir based on stream output filepath
        $absolutePath = Paths::getAbsolutePath($writer->getStream());
        $renderer     = new BaselineRenderer(dirname($absolutePath));
        $renderer->setWriter($writer);

        return $renderer;
    }
}
