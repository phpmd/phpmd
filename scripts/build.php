<?php

$root = realpath(__DIR__ . '/../') . '/';

$archiveName = 'phpmd.phar';
$changelog = file_get_contents($root . 'CHANGELOG', false, null, 0, 1024);
$version = preg_match('/phpmd-([\S]+)/', $changelog, $match) ? $match[1] : '@package_version@';

echo 'PHPMD ', $version, PHP_EOL, PHP_EOL;

$phar = new Phar($archiveName);
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'src/');
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'rulesets/');
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'vendor/');

$patchList = [
    'src/PHPMD.php',
    'src/TextUI/Command.php',
];
foreach ($patchList as $filePath) {
    $fileContent = str_replace('@package_version@', $version, file_get_contents($root . $filePath));
    $phar->addFromString($filePath, $fileContent);
}

// Set a custom stub
$customStubContent = file_get_contents($root . 'conf/phar_bootstrap.stub');
$customStubContent = str_replace('${archive.alias}', $archiveName, $customStubContent);
$phar->setStub($customStubContent);
