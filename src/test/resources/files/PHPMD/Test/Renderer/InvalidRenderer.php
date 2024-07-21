<?php

namespace PHPMD\Test\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;

class InvalidRenderer extends AbstractRenderer
{
    public function renderReport(Report $report): void
    {
    }
}
