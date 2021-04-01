<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractTest;
use PHPMD\Writer\StreamWriter;

/**
 * @coversDefaultClass \PHPMD\Renderer\RendererFactory
 */
class RendererFactoryTest extends AbstractTest
{
    /**
     * @covers ::createBaselineRenderer
     */
    public function testCreateBaselineRendererSuccessfully()
    {
        $writer = new StreamWriter(tmpfile());
        $renderer = RendererFactory::createBaselineRenderer($writer);

        static::assertSame($writer, $renderer->getWriter());
    }

    /**
     * @covers ::createBaselineRenderer
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Failed to determine absolute path for baseline file
     */
    public function testCreateBaselineRendererThrowsExceptionForInvalidStream()
    {
        $writer = new StreamWriter(STDOUT);
        RendererFactory::createBaselineRenderer($writer);
    }
}
