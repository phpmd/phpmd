<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheState;

class ResultCacheWriter
{
    public function __construct(
        private readonly string $filePath,
    ) {
    }

    public function write(ResultCacheState $state): void
    {
        $output = "<?php \n\nreturn ";
        $output .= var_export($state->toArray(), true);
        $output .= ";\n";

        file_put_contents($this->filePath, $output);
    }
}
