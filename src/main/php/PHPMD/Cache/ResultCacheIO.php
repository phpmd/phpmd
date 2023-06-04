<?php

namespace PHPMD\Cache;

class ResultCacheIO
{
    /**
     * @param string $filePath
     * @return ResultCacheState|null
     */
    public function fromFile($filePath)
    {
        if (file_exists($filePath) === false) {
            return null;
        }

        $state = require $filePath;

        return new ResultCacheState($state);
    }

    /**
     * @param string $filePath
     */
    public function toFile(ResultCacheState $state, $filePath)
    {
        $output = "<?php \n\nreturn ";
        $output .= var_export($state->getState(), true);
        $output .= ";\n";

        file_put_contents($filePath, $output);
    }
}
