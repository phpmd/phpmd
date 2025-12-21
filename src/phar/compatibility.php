<?php

function replaceInFiles(array $files, array $replacements)
{
    foreach ($files as $file) {
        echo "File $file:\n";

        if (!file_exists($file)) {
            echo "File not found.\n";

            continue;
        }

        $oldContent = @file_get_contents($file) ?: '';
        $newContents = $oldContent;

        foreach ($replacements as $key => $operation) {
            if ($operation instanceof Closure) {
                $newContents = call_user_func($operation, $newContents);
                unset($replacements[$key]);
            }
        }

        $newContent = strtr($newContents, $replacements);

        if ($oldContent !== $newContent) {
            file_put_contents($file, $newContent);
            echo "Content changed.\n";

            continue;
        }

        echo "Replace pattern not found.\n";
    }
}

// Mute PHP 8 deprecation notices
replaceInFiles(
    array(
        __DIR__ . '/../../vendor/symfony/config/Util/XmlUtils.php',
        __DIR__ . '/../../vendor/symfony/dependency-injection/Loader/XmlFileLoader.php',
    ),
    array(
        ' libxml_disable_entity_loader(' => ' @libxml_disable_entity_loader(',
    )
);

$source = __DIR__ . '/../../vendor/pdepend/pdepend/src/main/php';

$replacements = array(
    $source . '/PDepend/DependencyInjection/Configuration.php' => array(
        function ($contents) {
            global $source;

            $extract = (string)file_get_contents($source . '/Lazy/PDepend/DependencyInjection/Configuration.weak.php');
            $pattern = '(// <AbstractConfiguration>[\s\S]+</AbstractConfiguration>)';

            if (!preg_match($pattern, $extract, $match)) {
                return $contents;
            }

            return preg_replace($pattern, $match[0], $contents);
        },
    ),
    $source . '/Lazy/PDepend/DependencyInjection/Configuration.weak.php' => array(
        function () {
            return '';
        },
    ),
    $source . '/Lazy/PDepend/DependencyInjection/Configuration.strong.php' => array(
        function () {
            return '';
        },
    ),
);

foreach ($replacements as $file => $callbacks) {
    replaceInFiles(array($file), $callbacks);
}
