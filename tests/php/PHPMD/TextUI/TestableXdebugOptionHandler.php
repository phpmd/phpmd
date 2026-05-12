<?php

namespace PHPMD\TextUI;

/**
 * Testable subclass that replicates XdebugOptionHandler::restart logic
 * but captures the result instead of actually restarting the PHP process.
 *
 * This is necessary because XdebugHandler::restart() spawns a new process
 * via proc_open, which cannot be intercepted from a subclass (doRestart is private).
 * The logic under test is the command manipulation in XdebugOptionHandler::restart().
 */
class TestableXdebugOptionHandler extends XdebugOptionHandler
{
    /** @var list<string> */
    public array $capturedCommand = [];

    public bool $parentCalled = false;

    public string $stderrOutput = '';

    /**
     * Public entry point for tests.
     *
     * @param array<int, string> $command
     */
    public function doTestRestart(array $command): void
    {
        $this->parentCalled = false;
        $this->capturedCommand = [];
        $this->stderrOutput = '';

        // Capture stderr writes
        $tmpStream = fopen('php://memory', 'r+b');
        assert($tmpStream !== false);

        if (in_array('--xdebug', $command, true)) {
            if (($xdebugKey = array_search('--xdebug', $command, true)) !== false) {
                unset($command[$xdebugKey]);
            }
            if (($noConfigKey = array_search('-n', $command, true)) !== false) {
                unset($command[$noConfigKey]);
            }
            if (($configKey = array_search('-c', $command, true)) !== false) {
                unset(
                    $command[$configKey + 1],
                    $command[$configKey]
                );
            }

            $activateXdebugOptions[] = '-d xdebug.mode=debug';
            $activateXdebugOptions[] = '-d xdebug.start_with_request=on';

            array_splice($command, 1, 0, $activateXdebugOptions);

            fwrite($tmpStream, 'Restarting PHP Mess Detector with Xdebug enabled:' . PHP_EOL);
            fwrite($tmpStream, implode(' ', $command) . PHP_EOL);
            fwrite($tmpStream, PHP_EOL);
        }

        rewind($tmpStream);
        $this->stderrOutput = stream_get_contents($tmpStream) ?: '';
        fclose($tmpStream);

        if ($command) {
            $this->parentCalled = true;
            $this->capturedCommand = array_values($command);
        }
    }
}
