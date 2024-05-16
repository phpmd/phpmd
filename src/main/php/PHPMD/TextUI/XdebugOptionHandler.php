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
     * Rebuilds the run command with Xdebug enabled, instead, if CLI option "--xdebug" is used
     */
    protected function restart(array $command): void
    {
        if (in_array('--xdebug', $command, true)) {
            // Unset unwanted command arguments & options
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

            // The PHP INI entries to enable Xdebug
            $activateXdebugOptions[] = '-d xdebug.mode=debug';
            $activateXdebugOptions[] = '-d xdebug.start_with_request=on';

            // Inject the activating command options just after the PHP binary
            array_splice($command, 1, 0, $activateXdebugOptions);

            fwrite(STDERR, 'Restarting PHP Mess Detector with Xdebug enabled:' . PHP_EOL);
            fwrite(STDERR, implode(' ', $command) . PHP_EOL);
            fwrite(STDERR, PHP_EOL);
        }

        if ($command) {
            parent::restart($command);
        }
    }
}
