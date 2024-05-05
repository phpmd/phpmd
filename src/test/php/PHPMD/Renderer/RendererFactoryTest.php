<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractTestCase;
use PHPMD\Writer\StreamWriter;

/**
 * @coversDefaultClass \PHPMD\Renderer\RendererFactory
 */
class RendererFactoryTest extends AbstractTestCase
{
    /**
     * @covers ::createBaselineRenderer
     */
    public function testCreateBaselineRendererSuccessfully(): void
    {
        $writer = new StreamWriter(tmpfile());
        $renderer = RendererFactory::createBaselineRenderer($writer);

        static::assertSame($writer, $renderer->getWriter());
    }
}
