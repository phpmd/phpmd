<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractTestCase;
use Symfony\Component\Console\Output\BufferedOutput;

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
        $writer = new BufferedOutput();
        $renderer = RendererFactory::createBaselineRenderer($writer);

        static::assertSame($writer, $renderer->getWriter());
    }
}
