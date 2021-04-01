<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;
use PHPMD\Utility\Paths;

class BaselineRenderer extends AbstractRenderer
{
    /** @var string */
    private $basePath;

    /**
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    public function renderReport(Report $report)
    {
        $writer = $this->getWriter();
        $writer->write('<?xml version="1.0"?>' . PHP_EOL);
        $writer->write('<phpmd-baseline>' . PHP_EOL);

        foreach ($report->getRuleViolations() as $violation) {
            $rule     = $violation->getRule();
            $filepath = $violation->getFileName();

            $xmlTag = sprintf(
                '  <violation rule="%s" file="%s"/>' . PHP_EOL,
                get_class($rule),
                Paths::getRelativePath($this->basePath, $filepath)
            );
            $writer->write($xmlTag);
        }

        $writer->write('</phpmd-baseline>' . PHP_EOL);
    }
}
