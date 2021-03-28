<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;

class BaselineRenderer extends AbstractRenderer
{
    /** @var string */
    private $baseDir;

    /**
     * @param string $baseDir
     */
    public function __construct($baseDir)
    {
        $this->baseDir = rtrim(str_replace("\\", "/", $baseDir), '/') . '/';
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
                $this->getRelativePath($filepath)
            );
            $writer->write($xmlTag);
        }

        $writer->write('</phpmd-baseline>' . PHP_EOL);
    }

    /**
     * Transform the given absolute path to the relative path within the input path
     *
     * @param string $filepath
     * @return string
     */
    private function getRelativePath($filepath)
    {
        // transform all slashes to forward slashes
        $filepath = str_replace("\\", "/", $filepath);

        // subtract base dir from filepath if there's a match
        if (stripos($filepath, $this->baseDir) === 0) {
            $filepath = substr($filepath, strlen($this->baseDir));
        }

        return $filepath;
    }
}
