<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractTest;
use PHPMD\Writer\StreamWriter;
use RuntimeException;

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
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unable to determine the realpath for
     */
    public function testCreateBaselineRendererThrowsExceptionForInvalidStream()
    {
        $writer = new StreamWriter(STDOUT);
        RendererFactory::createBaselineRenderer($writer);
    }
}
