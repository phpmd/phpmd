<?php

namespace PHPMD\Renderer;

use InvalidArgumentException;
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

    /**
     * @throws InvalidArgumentException
     */
    public function getRenderer(string $format): RendererInterface
    {
        return match ($format) {
            'ansi' => new AnsiRenderer(),
            'checkstyle' => new CheckStyleRenderer(),
            'github' => new GitHubRenderer(),
            'gitlab' => new GitLabRenderer(),
            'html' => new HTMLRenderer(),
            'json' => new JSONRenderer(),
            'sarif' => new SARIFRenderer(),
            'text' => new TextRenderer(),
            'xml' => new XMLRenderer(),
            default => $this->getCustomRenderer($format),
        };
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getCustomRenderer(string $format): RendererInterface
    {
        if (!class_exists($format)) {
            throw new InvalidArgumentException(
                sprintf('No renderer supports the format "%s".', $format),
                RendererInterface::INPUT_ERROR
            );
        }

        $renderer = new $format();

        if (!$renderer instanceof RendererInterface) {
            throw new InvalidArgumentException(
                sprintf('Renderer class "%s" does not implement "%s".', $format, RendererInterface::class),
                RendererInterface::INPUT_ERROR
            );
        }

        return $renderer;
    }
}
