#!/usr/bin/env php
<?php
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once 'PHP/PMD.php';

if (!isset($_SERVER['argv']) && !isset($argv)) {
    fwrite(STDERR, 'Please enable the "register_argc_argv" directive in your php.ini', PHP_EOL);
    exit(1);
} else if (!isset($argv)) {
    $argv = $_SERVER['argv'];
}

PHP_PMD::main($argv);
exit(0);
?>
