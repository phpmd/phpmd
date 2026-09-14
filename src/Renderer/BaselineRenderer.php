<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Report;
use PHPMD\Utility\Paths;

final class BaselineRenderer extends AbstractRenderer
{
    /**
     * @param BaselineMode $mode Generate writes every violation of the report,
     *                           Update writes only the violations that are already baselined.
     */
    public function __construct(
        private readonly string $basePath,
        private readonly BaselineMode $mode = BaselineMode::Generate,
    ) {
    }

    public function renderReport(Report $report): void
    {
        // keep track of which violations have been written, to avoid duplicates in the baseline
        $registered = [];

        $violations = $this->mode === BaselineMode::Update
            ? $report->getBaselinedRuleViolations()
            : $report->getRuleViolations();

        $writer = $this->getWriter();
        $writer->write('<?xml version="1.0"?>' . PHP_EOL);
        $writer->write('<phpmd-baseline>' . PHP_EOL);

        foreach ($violations as $violation) {
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
