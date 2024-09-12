<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;
use PHPMD\Utility\Paths;

final class BaselineRenderer extends AbstractRenderer
{
    public function __construct(
        private readonly string $basePath,
    ) {
    }

    public function renderReport(Report $report): void
    {
        // keep track of which violations have been written, to avoid duplicates in the baseline
        $registered = [];

        $writer = $this->getWriter();
        $writer->write('<?xml version="1.0"?>' . PHP_EOL);
        $writer->write('<phpmd-baseline>' . PHP_EOL);

        foreach ($report->getRuleViolations() as $violation) {
            $ruleName = $violation->getRule()::class;
            $filePath = Paths::getRelativePath($this->basePath, (string) $violation->getFileName());
            $methodName = $violation->getMethodName();

            // deduplicate similar violations
            $key = $ruleName . $filePath . $methodName;
            if (isset($registered[$key])) {
                continue;
            }

            $xmlTag = sprintf(
                '  <violation rule="%s" file="%s"%s/>' . PHP_EOL,
                $ruleName,
                $filePath,
                $methodName === null ? '' : ' method="' . $methodName . '"'
            );
            $writer->write($xmlTag);
            $registered[$key] = true;
        }

        $writer->write('</phpmd-baseline>' . PHP_EOL);
    }
}
