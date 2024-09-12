<?php

namespace PHPMD\Renderer;

use PHPMD\AbstractWriter;
use PHPMD\Report;

interface RendererInterface
{
    public const INPUT_ERROR = 23;

    public function setWriter(AbstractWriter $writer): void;

    public function start(): void;

    public function renderReport(Report $report): void;

    public function end(): void;
}
