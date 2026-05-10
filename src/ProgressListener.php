<?php

/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD;

use PDepend\Metrics\Analyzer;
use PDepend\ProcessListener;
use PDepend\Source\ASTVisitor\AbstractASTVisitListener;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressListener extends AbstractASTVisitListener implements ProcessListener
{
    private ProgressBar $progressBar;

    public function __construct(
        protected OutputInterface $output,
    ) {
    }

    public function startParseProcess(int $fileCount): void
    {
        $this->progressBar = new ProgressBar($this->output, $fileCount);
        $this->progressBar->setBarCharacter('▓');
        $this->progressBar->setEmptyBarCharacter('░');
        $this->progressBar->setProgressCharacter('░');
        $this->progressBar->start();
    }

    public function endParseProcess(): void
    {
        $this->progressBar->finish();
        $this->progressBar->clear();
    }

    public function startFileParsing(): void
    {
    }

    public function endFileParsing(): void
    {
        $this->progressBar->advance();
    }

    public function startAnalyzeProcess(): void
    {
    }

    public function endAnalyzeProcess(): void
    {
    }

    public function startLogProcess(): void
    {
    }

    public function endLogProcess(): void
    {
    }

    public function startAnalyzer(Analyzer $analyzer): void
    {
    }

    public function endAnalyzer(Analyzer $analyzer): void
    {
    }
}
