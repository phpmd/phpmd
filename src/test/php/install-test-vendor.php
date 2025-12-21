<?php

$cwd = getcwd();
chdir(__DIR__ . '/..');
$phar = $cwd . '/composer.phar';
$composer = file_exists($phar) ? 'php ' . escapeshellarg(realpath($phar)) : 'composer';
$command = $composer . ' update --ignore-platform-req=php+';

echo "> $command\n";
echo shell_exec($command);

require __DIR__ . '/fix-php-compatibility.php';

chdir($cwd);
