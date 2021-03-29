<?php

namespace PHPMD\Baseline;

use RuntimeException;

class BaselineSetFactory
{
    /**
     * @param string $filename
     * @return BaselineSet
     * @throws RuntimeException
     */
    public function fromFile($filename)
    {
        if (file_exists($filename) === false) {
            throw new RuntimeException('Unknown file: ' . $filename);
        }

        $xml = @simplexml_load_string(file_get_contents($filename));
        if ($xml === false) {
            throw new RuntimeException('Unable to read xml from: ' . $filename);
        }

        $baselineSet = new BaselineSet();

        foreach ($xml->children() as $node) {
            if ($node->getName() !== 'violation') {
                continue;
            }

            if (isset($node['rule']) === false) {
                throw new RuntimeException('Missing `rule` attribute in `violation` in ' . $filename);
            }

            if (isset($node['file']) === false) {
                throw new RuntimeException('Missing `file` attribute in `violation` in ' . $filename);
            }

            $baselineSet->addEntry(new ViolationBaseline((string)$node['rule'], (string)$node['file']));
        }

        return $baselineSet;
    }
}
