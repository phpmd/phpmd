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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD\TextUI;

use PDepend\TextUI\Command as PdependCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Hidden Symfony Console command that allows pdepend's parallel worker
 * processes to be spawned through PHPMD's binary.
 *
 * When pdepend runs in multi-threaded mode as a library inside PHPMD,
 * it needs to spawn child processes that can bootstrap pdepend's worker.
 * This command provides that entry point, similar to how PHPStan
 * registers a `worker` subcommand for its parallel analysis.
 */
#[AsCommand(
    name: 'pdepend:worker',
    description: 'Internal command used by pdepend for parallel processing',
    hidden: true,
)]
final class PdependWorkerCommand extends SymfonyCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return PdependCommand::main();
    }
}
