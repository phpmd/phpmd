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
        // determine basedir based on output filepath
        $absolutePath = Paths::getAbsolutePath($writer->getStream());
        if ($absolutePath === null) {
            throw new RuntimeException('Failed to determine absolute path for baseline file');
        }
        $renderer = new BaselineRenderer(dirname($absolutePath));
        $renderer->setWriter($writer);

        return $renderer;
    }
}
