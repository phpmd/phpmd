<?php

namespace PHPMD\TextUI;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PHPMD\TextUI\XdebugOptionHandler
 */
class XdebugOptionHandlerTest extends TestCase
{
    /**
     * @covers ::restart
     */
    public function testRestartWithXdebugOptionEnablesXdebug(): void
    {
        $handler = new TestableXdebugOptionHandler('PHPMD');
        $command = ['php', '-n', '-c', '/tmp/php.ini', 'bin/phpmd', '--xdebug', 'src/', 'text', 'cleancode'];

        $handler->doTestRestart($command);

        static::assertTrue($handler->parentCalled);
        $result = $handler->capturedCommand;

        // --xdebug should be removed
        static::assertNotContains('--xdebug', $result);

        // -n should be removed
        static::assertNotContains('-n', $result);

        // -c and its value should be removed
        static::assertNotContains('-c', $result);
        static::assertNotContains('/tmp/php.ini', $result);

        // Xdebug activation options should be injected after the PHP binary
        static::assertSame('php', $result[0]);
        static::assertSame('-d xdebug.mode=debug', $result[1]);
        static::assertSame('-d xdebug.start_with_request=on', $result[2]);

        // The remaining args should still be present
        static::assertContains('bin/phpmd', $result);
        static::assertContains('src/', $result);
        static::assertContains('text', $result);
        static::assertContains('cleancode', $result);
    }

    /**
     * @covers ::restart
     */
    public function testRestartWithoutXdebugOptionPassesCommandUnchanged(): void
    {
        $handler = new TestableXdebugOptionHandler('PHPMD');
        $command = ['php', 'bin/phpmd', 'src/', 'text', 'cleancode'];

        $handler->doTestRestart($command);

        static::assertTrue($handler->parentCalled);
        static::assertSame($command, $handler->capturedCommand);
    }

    /**
     * @covers ::restart
     */
    public function testRestartWithEmptyCommandDoesNotCallParent(): void
    {
        $handler = new TestableXdebugOptionHandler('PHPMD');

        $handler->doTestRestart([]);

        static::assertFalse($handler->parentCalled);
    }

    /**
     * @covers ::restart
     */
    public function testRestartWithXdebugOptionWithoutNOrCFlags(): void
    {
        $handler = new TestableXdebugOptionHandler('PHPMD');
        $command = ['php', 'bin/phpmd', '--xdebug', 'src/', 'text', 'cleancode'];

        $handler->doTestRestart($command);

        static::assertTrue($handler->parentCalled);
        $result = $handler->capturedCommand;

        static::assertNotContains('--xdebug', $result);
        static::assertSame('php', $result[0]);
        static::assertSame('-d xdebug.mode=debug', $result[1]);
        static::assertSame('-d xdebug.start_with_request=on', $result[2]);
        static::assertSame('bin/phpmd', $result[3]);
        static::assertSame('src/', $result[4]);
    }

    /**
     * @covers ::restart
     */
    public function testRestartWritesToStderrWhenXdebugOptionPresent(): void
    {
        $handler = new TestableXdebugOptionHandler('PHPMD');
        $command = ['php', 'bin/phpmd', '--xdebug', 'src/', 'text', 'cleancode'];

        $handler->doTestRestart($command);

        static::assertStringContainsString('Restarting PHP Mess Detector with Xdebug enabled:', $handler->stderrOutput);
    }

    /**
     * @covers ::restart
     */
    public function testRestartDoesNotWriteToStderrWithoutXdebugOption(): void
    {
        $handler = new TestableXdebugOptionHandler('PHPMD');
        $command = ['php', 'bin/phpmd', 'src/', 'text', 'cleancode'];

        $handler->doTestRestart($command);

        static::assertSame('', $handler->stderrOutput);
    }
}
