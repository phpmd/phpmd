<?php

namespace PHPMD\Baseline;

use RuntimeException;

class BaselineSetFactory
{
    /**
     * @param string $filename
     * @return BaselineSet
     */
    public function fromFile($filename)
    {
        $libxml = libxml_use_internal_errors(true);
        $xml    = simplexml_load_string(file_get_contents($filename));
        if ($xml === false) {
            libxml_use_internal_errors($libxml);
            throw new RuntimeException(trim(libxml_get_last_error()->message));
        }

        $baselineSet = new BaselineSet();

        foreach ($xml->children() as $node) {
            if ($node->getName() !== 'violation') {
                continue;
            }
            $baselineSet->addEntry(new ViolationBaseline((string)$node->rule, (string)$node->filename));
        }

        return $baselineSet;
    }
}
