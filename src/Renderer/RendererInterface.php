<?php

namespace PHPMD\Renderer;

use PHPMD\Report;
use Symfony\Component\Console\Output\OutputInterface;

interface RendererInterface
{
    public const INPUT_ERROR = 23;

    public function setWriter(OutputInterface $writer): void;

    public function start(): void;

    public function renderReport(Report $report): void;

    public function end(): void;
}
