<?php

$root = realpath(__DIR__ . '/../') . '/';

$archiveName = 'phpmd.phar';
$version = parse_ini_file($root . 'build.properties')['project.version'] ?? '@package_version@';

echo 'PHPMD ', $version, PHP_EOL, PHP_EOL;

$phar = new Phar($archiveName);
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'src\/main/');
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'vendor\/autoload\.php/');
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'vendor\/composer/');
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'vendor\/pdepend/');
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'vendor\/psr/');
$phar->buildFromDirectory($root, '/^'.preg_quote($root, '/').'vendor\/symfony(?!.*\/.*\/Test\/).*$/');

$patchList = [
    'src/main/php/PHPMD/PHPMD.php',
    'src/main/php/PHPMD/TextUI/Command.php',
];
foreach ($patchList as $filePath) {
    $fileContent = str_replace('@package_version@', $version, file_get_contents($root . $filePath));
    $phar->addFromString($filePath, $fileContent);
}

// Set a custom stub
$customStubContent = file_get_contents($root . 'src/conf/phar_bootstrap.stub');
$customStubContent = str_replace('${archive.alias}', $archiveName, $customStubContent);
$phar->setStub($customStubContent);
