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
}
