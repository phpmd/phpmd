<?php

namespace PHPMD\TextUI;

use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\Language\PHP\PHPBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PdependWorkerCommand::class)]
class PdependWorkerCommandTest extends TestCase
{
    /**
     * The worker is spawned by pdepend as `php bin/phpmd pdepend:worker --worker`,
     * so this test drives the real subprocess the same way, since the bug only
     * showed up through $_SERVER['argv'] as seen by that process, not through
     * PHPUnit's own invocation of the command object.
     */
    public function testWorkerParsesFileAndReturnsSerializedNamespaces(): void
    {
        $projectRoot = dirname(__DIR__, 4);
        $file = $projectRoot . '/src/Rule.php';

        $process = proc_open(
            [PHP_BINARY, $projectRoot . '/bin/phpmd', 'pdepend:worker', '--worker'],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $projectRoot
        );

        static::assertIsResource($process);

        fwrite($pipes[0], $file . "\n");
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        static::assertNotFalse($output);
        $line = trim((string) strtok($output, "\n"));

        static::assertNotSame('', $line, 'Worker produced no output. Stderr: ' . $stderr);

        $decoded = base64_decode($line, true);
        static::assertNotFalse($decoded, 'Worker output was not valid base64: ' . $line);

        // Unserializing an ASTInterface/ASTClass triggers __wakeup(), which
        // registers the node with the currently active GlobalBuilderContext.
        new PHPBuilder();

        $data = unserialize($decoded);
        static::assertInstanceOf(ASTArtifactList::class, $data);
    }
}
