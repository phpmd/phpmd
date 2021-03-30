<?php

namespace PHPMD\Utility;

class Paths
{
    /**
     * Append $pathB to $pathA and apply the correct amount of slashes between them
     *
     * @param string $pathA
     * @param string $pathB
     * @return string
     */
    public static function concat($pathA, $pathB)
    {
        $pathA = rtrim(str_replace('\\', '/', $pathA), '/');
        $pathB = ltrim(str_replace('\\', '/', $pathB), '/');
        return $pathA . '/' . $pathB;
    }

    /**
     * Transform the given absolute path to the relative path within based on the base path.
     *
     * @param string $basePath
     * @param string $filePath
     * @return string
     */
    public static function getRelativePath($basePath, $filePath)
    {
        // normalize slashes and ensure base path ends with slash
        $basePath = rtrim(str_replace('\\', '/', $basePath), '/') . '/';
        $filePath = str_replace('\\', '/', $filePath);

        // subtract base dir from filepath if there's a match
        if (stripos($filePath, $basePath) === 0) {
            $filePath = substr($filePath, strlen($basePath));
        }

        return $filePath;
    }

    /**
     * Derive the absolute path from the given resource
     * @param resource $resource
     * @return string
     */
    public static function getAbsolutePath($resource)
    {
        $metaData = stream_get_meta_data($resource);
        if (isset($metaData['uri']) === false) {
            return null;
        }

        $absolutePath = realpath($metaData['uri']);
        if ($absolutePath === false) {
            return null;
        }

        return $absolutePath;
    }
}
