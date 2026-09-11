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

namespace PHPMD\TextUI;

use PHPMD\ParserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParserFactory::class)]
class ThreadParityTest extends TestCase
{
    /** The fixtures are reported by the rules below, and there are enough of them to spread over the workers. */
    private const CORPUS = __DIR__ . '/../../../resources/files/TextUI/threads';

    public function testTheThreadCountDoesNotChangeWhatIsReported(): void
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD') && !extension_loaded('sockets')) {
            static::markTestSkipped('Parsing in worker processes needs ext-sockets on Windows.');
        }

        $serial = $this->analyze(1);
        $parallel = $this->analyze(4);

        // Check  status of things that rely on the token stream.
        static::assertStringContainsString('ExcessiveMethodLength', $serial, 'The corpus no longer covers "loc".');
        static::assertStringContainsString('ExcessiveClassLength', $serial, 'The corpus no longer covers "eloc".');

        static::assertSame($serial, $parallel);
    }

    private function analyze(int $threads): string
    {
        $projectRoot = dirname(__DIR__, 4);

        $process = proc_open(
            [
                PHP_BINARY,
                $projectRoot . '/bin/phpmd',
                'analyze',
                '--no-ansi',
                '--no-progress',
                '--no-cache',
                '--format=text',
                '--threads=' . $threads,
                '--ruleset=' . self::CORPUS . '/parity.xml',
                self::CORPUS . '/source',
            ],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $projectRoot,
            ['GITHUB_ACTIONS' => 'false'] + getenv()
        );

        static::assertIsResource($process);

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        static::assertNotFalse($output);
        static::assertSame('', $stderr, sprintf('Analyzing with %d thread(s) wrote to stderr.', $threads));
        static::assertNotSame('', $output, sprintf('Analyzing with %d thread(s) reported nothing.', $threads));

        return $output;
    }
}
