<?php

namespace PHPMD\Utility;

use RuntimeException;

final class Paths
{
    /**
     * Transform the given absolute path to the relative path based on the given base path.
     */
    public static function getRelativePath(string $basePath, string $filePath): string
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
     * Concat pathB to pathA with a single path separators between them
     */
    public static function concat(string $pathA, string $pathB): string
    {
        return rtrim(str_replace('\\', '/', $pathA), '/') . '/' . ltrim(str_replace('\\', '/', $pathB), '/');
    }

    /**
     * Get the realpath of the given path or exception on failure
     * @throws RuntimeException
     */
    public static function getRealPath(string $path): string
    {
        $absolutePath = realpath($path);
        if (!$absolutePath) {
            throw new RuntimeException('Unable to determine the realpath for: ' . $path);
        }

        return $absolutePath;
    }
}
