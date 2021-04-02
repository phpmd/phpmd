<?php

namespace PHPMD\Utility;

use RuntimeException;

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
     * Transform the given absolute path to the relative path based on the given base path.
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
     * @throws RuntimeException
     */
    public static function getAbsolutePath($resource)
    {
        $metaData = stream_get_meta_data($resource);
        return self::getRealPath($metaData['uri']);
    }

    /**
     * Get the realpath of the given path or exception on failure
     * @param string $path
     * @return string
     * @throws RuntimeException
     */
    public static function getRealPath($path)
    {
        $absolutePath = realpath($path);
        if ($absolutePath === false) {
            throw new RuntimeException('Unable to determine the realpath for: ' . $path);
        }

        return $absolutePath;
    }
}
