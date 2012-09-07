#!/usr/bin/env php
<?php


if (file_exists(__DIR__ . '/../../../../autoload.php')) {
    // phpmd is part of a composer installation
    require_once __DIR__ . '/../../../../autoload.php';
} else {

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
}

// Allow as much memory as possible by default
if (extension_loaded('suhosin') && is_numeric(ini_get('suhosin.memory_limit'))) {
    $limit = ini_get('memory_limit');
    if (preg_match('(^(\d+)([BKMGT]))', $limit, $match)) {
        $shift = array('B' => 0, 'K' => 10, 'M' => 20, 'G' => 30, 'T' => 40);
        $limit = ($match[1] * (1 << $shift[$match[2]]));
    }
    if (ini_get('suhosin.memory_limit') > $limit && $limit > -1) {
        ini_set('memory_limit', ini_get('suhosin.memory_limit'));
    }
} else {
    ini_set('memory_limit', -1);
}

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
