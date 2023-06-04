<?php

namespace PHPMD\Cache;

class ResultCacheWriter
{
    /** @var string */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {

        $this->filePath = $filePath;
    }

    public function write(ResultCacheState $state)
    {
        $output = "<?php \n\nreturn ";
        $output .= var_export($state->getState(), true);
        $output .= ";\n";

        file_put_contents($this->filePath, $output);
    }
}
