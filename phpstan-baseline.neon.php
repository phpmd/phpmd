<?php declare(strict_types = 1);

$includes = [];
if (PHP_VERSION_ID < 80300) {
	$includes[] = __DIR__ . '/phpstan-baseline-pre-8.3.neon';
}

$config = [];
$config['includes'] = $includes;

// overrides config.platform.php in composer.json
$config['parameters']['phpVersion'] = PHP_VERSION_ID;

return $config;
