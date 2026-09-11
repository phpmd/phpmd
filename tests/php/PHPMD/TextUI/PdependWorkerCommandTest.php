<?php

namespace PHPMD\TextUI;

use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\WorkerProtocol;
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
        static::assertNotSame('', $output, 'Worker produced no output. Stderr: ' . $stderr);

        $buffer = (string) $output;
        $payload = WorkerProtocol::unframe($buffer);

        static::assertNotNull($payload, 'Worker output was not a complete frame. Stderr: ' . $stderr);

        // Unserializing an ASTInterface/ASTClass triggers __wakeup(), which
        // registers the node with the currently active GlobalBuilderContext.
        new PHPBuilder();

        $data = unserialize($payload);

        static::assertIsArray($data);

        $namespaces = $data['namespaces'] ?? null;
        static::assertInstanceOf(ASTArtifactList::class, $namespaces);

        $packedTokens = $data['tokens'] ?? null;
        static::assertIsString($packedTokens);
        static::assertNotSame('', $packedTokens);

        $index = WorkerProtocol::indexTokens($packedTokens);
        static::assertNotSame([], $index);

        $interface = null;
        foreach ($namespaces as $namespace) {
            if (!$namespace instanceof ASTNamespace) {
                continue;
            }

            foreach ($namespace->getInterfaces() as $candidate) {
                if ($candidate->getImage() === 'Rule') {
                    $interface = $candidate;
                }
            }
        }

        static::assertInstanceOf(ASTInterface::class, $interface, 'The worker did not report the parsed interface.');
        static::assertArrayHasKey($interface->getId(), $index, 'The interface travelled without its tokens.');

        [$offset, $length] = $index[$interface->getId()];
        $tokens = WorkerProtocol::unpackTokens($packedTokens, $offset, $length);

        static::assertNotSame([], $tokens);

        $images = [];
        foreach ($tokens as $token) {
            static::assertNotSame('', $token->image, 'A token arrived without its image.');
            static::assertGreaterThan(0, $token->startLine, 'A token arrived without its position.');
            $images[] = $token->image;
        }

        // Declared by the interface this test parses, so the stream has to carry them.
        static::assertContains('HIGHEST_PRIORITY', $images);
        static::assertContains('apply', $images);
    }
}
