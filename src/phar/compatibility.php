<?php

function replaceInFiles(array $files, array $replacements)
{
    foreach ($files as $file) {
        $oldContent = file_get_contents($file);
        $newContent = strtr($oldContent, $replacements);

        if ($oldContent !== $newContent) {
            file_put_contents($file, $newContent);
        }
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
