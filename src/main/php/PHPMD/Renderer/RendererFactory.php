<?php

namespace PHPMD\Renderer;

use InvalidArgumentException;
use PHPMD\AbstractWriter;
use PHPMD\TextUI\CommandLineOptions;

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
        if (class_exists($format)) {
            $renderer = new $format();
            if ($renderer instanceof RendererInterface) {
                return $renderer;
            }
        }

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
            default => throw new InvalidArgumentException(
                sprintf('No renderer supports the format "%s".', $format),
                RendererInterface::INPUT_ERROR
            ),
        };
    }
}
