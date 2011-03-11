#!/usr/bin/env php
<?php

// PEAR installation workaround
if (strpos('@package_version@', '@package_version') === 0) {
    set_include_path(
        dirname(__FILE__) . '/../main/php' .
        PATH_SEPARATOR .
        dirname(__FILE__) . '/../../lib/pdepend/src/main/php' .
        PATH_SEPARATOR .
        '.'
    );
}

// Disable memory_limit
ini_set('memory_limit', -1);

// Check php setup for cli arguments
if (!isset($_SERVER['argv']) && !isset($argv)) {
    fwrite(STDERR, 'Please enable the "register_argc_argv" directive in your php.ini', PHP_EOL);
    exit(1);
} else if (!isset($argv)) {
    $argv = $_SERVER['argv'];
}

// Load command line utility
require_once 'PHP/PMD/TextUI/Command.php';

// Run command line interface
exit(PHP_PMD_TextUI_Command::main($argv));
