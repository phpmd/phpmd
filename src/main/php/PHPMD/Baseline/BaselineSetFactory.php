<?php

namespace PHPMD\Baseline;

use RuntimeException;

class BaselineSetFactory
{
    /**
     * Read the baseline violations from the given filename path. Append the baseDir to all the filepaths within
     * the baseline file.
     *
     * @param string $fileName
     * @return BaselineSet
     * @throws RuntimeException
     */
    public static function fromFile($fileName)
    {
        if (file_exists($fileName) === false) {
            throw new RuntimeException('Unable to locate the baseline file at: ' . $fileName);
        }

        $xml = @simplexml_load_string(file_get_contents($fileName));
        if ($xml === false) {
            throw new RuntimeException('Unable to read xml from: ' . $fileName);
        }

        $baselineSet = new BaselineSet();
        foreach ($xml->children() as $node) {
            if ($node->getName() !== 'violation') {
                continue;
            }

            if (isset($node['rule']) === false) {
                throw new RuntimeException('Missing `rule` attribute in `violation` in ' . $fileName);
            }

            if (isset($node['file']) === false) {
                throw new RuntimeException('Missing `file` attribute in `violation` in ' . $fileName);
            }

            $methodName = null;
            if (isset($node['method']) === true && ((string)$node['method']) !== '') {
                $methodName = (string)($node['method']);
            }

            $baselineSet->addEntry(new ViolationBaseline((string)$node['rule'], (string)$node['file'], $methodName));
        }

        return $baselineSet;
    }
}
