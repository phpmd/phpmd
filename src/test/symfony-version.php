<?php

$installed = require __DIR__.'/../../vendor/composer/installed.php';
$versions = $installed['versions'];

$symfonyPackages = array(
    'config',
    'dependency-injection',
    'filesystem',
    'yaml'
);

foreach ($symfonyPackages as $package) {
    echo "$package: ".
        (isset($versions['symfony/'.$package]['pretty_version'])
            ? $versions['symfony/'.$package]['pretty_version']
            : 'not installed').
        "\n";
}
