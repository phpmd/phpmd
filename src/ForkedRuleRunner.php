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

use PDepend\Source\AST\ASTArtifact;
use Phar;
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Rule\CleanCode\StaticAccess;
use PHPMD\Rule\Design\ExitExpression;
use RuntimeException;
use Throwable;

/**
 * Forking is the imple way to thread rule processing since the only thing that
 * changes at this point is the reported violations.
 */
final class ForkedRuleRunner
{
    private readonly ViolationTransfer $transfer;

    /**
     * @param list<RuleSet> $ruleSets
     */
    public function __construct(
        private readonly int $workers,
        Report $report,
        array $ruleSets,
    ) {
        $this->transfer = new ViolationTransfer($report, $ruleSets);
    }

    /**
     * Applies the rules to every file, and collects what the processes found.
     *
     * Returns false when the rules have to be applied by the caller.
     *
     * @param list<list<ASTArtifact>> $files The artifacts of each file.
     * @param callable(ASTArtifact): void $apply Applies the rules to one artifact.
     * @throws RuntimeException
     */
    public function run(array $files, callable $apply): bool
    {
        $numberOfWorkers = min($this->workers, count($files));

        // Don't use this runner unless pcntl_fork is available and we actually need multiple workers
        if (!$this->isSupported() || $numberOfWorkers < 2) {
            return false;
        }

        $results = $this->createResults($numberOfWorkers);

        if ($results === null) {
            return false;
        }

        $processIds = $this->fork($files, $apply, $results);

        if ($processIds === null) {
            return false;
        }

        foreach ($processIds as $processId) {
            // A process killed outright never got to write its result.
            if ($this->endedOnSignal($processId)) {
                throw new RuntimeException('A process applying the rules was killed before it finished.');
            }
        }

        foreach ($results as $result) {
            $this->transfer->import($result);
        }

        return true;
    }

    /**
     * Whether this process is one that may be forked at all.
     */
    #[SuppressWarnings(StaticAccess::class)]
    private function isSupported(): bool
    {
        if (!function_exists('pcntl_fork')) {
            return false;
        }

        // Every phar:// read is a seek and a read on the one handle libphar
        // keeps per archive, and forking hands that handle's single file offset
        // to every child at once, so two children reading the archive read each
        // other's bytes. Nothing in userland can reopen that handle, so a phar
        // release applies its rules in one process.
        return Phar::running(false) === '';
    }

    /**
     * Returns null when the pool could not be started.
     *
     * @param list<list<ASTArtifact>> $files
     * @param callable(ASTArtifact): void $apply
     * @param list<resource> $results One file per worker to report back through.
     * @return list<int>|null
     */
    private function fork(array $files, callable $apply, array $results): ?array
    {
        $processIds = [];

        foreach ($results as $worker => $result) {
            $processId = pcntl_fork();

            if ($processId === -1) {
                foreach ($processIds as $startedId) {
                    $this->endedOnSignal($startedId);
                }

                return null;
            }

            if ($processId === 0) {
                $this->applyToShare(count($results), $worker, $files, $apply, $result);
            }

            $processIds[] = $processId;
        }

        return $processIds;
    }

    /**
     * Applies the rules to this worker's share of the files and writes what
     * they found to $target.
     *
     * @param list<list<ASTArtifact>> $files
     * @param callable(ASTArtifact): void $apply
     * @param resource $target
     */
    #[SuppressWarnings(ExitExpression::class)]
    private function applyToShare(int $workers, int $worker, array $files, callable $apply, $target): never
    {
        $status = 0;

        try {
            foreach ($files as $index => $artifacts) {
                if ($index % $workers !== $worker) {
                    continue;
                }

                foreach ($artifacts as $artifact) {
                    $apply($artifact);
                }
            }

            $this->transfer->export($target);
        } catch (Throwable $throwable) {
            $this->transfer->exportError($target, $throwable->getMessage());
            $status = 1;
        }

        // Clear any buffer inherited by the parent process.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        exit($status);
    }

    private function endedOnSignal(int $processId): bool
    {
        $status = 0;
        pcntl_waitpid($processId, $status);

        return is_int($status) && pcntl_wifsignaled($status);
    }

    /**
     * @return list<resource>|null
     */
    private function createResults(int $workers): ?array
    {
        $results = [];

        for ($worker = 0; $worker < $workers; $worker++) {
            $result = tmpfile();

            if ($result === false) {
                return null;
            }

            $results[] = $result;
        }

        return $results;
    }
}
