<?php

namespace PHPMD\TextUI;

use Composer\XdebugHandler\XdebugHandler;

/**
 * Xdebug CLI Option Handler
 *
 * Enables instead of disables Xdebug, if called with "--xdebug" CLI option.
 */
class XdebugOptionHandler extends XdebugHandler
{
    /**
     * Rebuilds the run command with Xdebug enabled, instead, if CLI option "--xdebug" is used.
     *
     * @param list<string> $command
     */
    protected function restart(array $command): void
    {
        if (in_array('--xdebug', $command, true)) {
            // Unset unwanted command arguments & options
            $xdebugKey = array_search('--xdebug', $command, true);
            if ($xdebugKey !== false) {
                unset($command[$xdebugKey]);
            }
            $noConfigKey = array_search('-n', $command, true);
            if ($noConfigKey !== false) {
                unset($command[$noConfigKey]);
            }
            $configKey = array_search('-c', $command, true);
            if ($configKey !== false) {
                unset(
                    $command[$configKey + 1],
                    $command[$configKey]
                );
            }

            // The PHP INI entries to enable Xdebug
            $xdebugOptions = [
                '-d xdebug.mode=debug',
                '-d xdebug.start_with_request=on',
            ];

            // Inject the activating command options just after the PHP binary
            array_splice($command, 1, 0, $xdebugOptions);

            fwrite(STDERR, 'Restarting PHP Mess Detector with Xdebug enabled:' . PHP_EOL);
            fwrite(STDERR, implode(' ', $command) . PHP_EOL);
            fwrite(STDERR, PHP_EOL);
        }

        if ($command) {
            parent::restart($command);
        }
    }
}
