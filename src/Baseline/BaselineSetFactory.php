<?php

namespace PHPMD\Baseline;

use PHPMD\Exception\RuntimeException;
use SimpleXMLElement;

final class BaselineSetFactory
{
    /**
     * Read the baseline violations from the given filename path. Append the baseDir to all the filepaths within
     * the baseline file.
     *
     * @throws RuntimeException
     */
    public static function fromFile(string $fileName): BaselineSet
    {
        $xml = self::loadXml($fileName);
        $baselineSet = new BaselineSet();

        foreach ($xml->children() as $node) {
            if ($node->getName() !== 'violation') {
                continue;
            }

            if (!isset($node['rule'])) {
                throw new RuntimeException('Missing `rule` attribute in `violation` in ' . $fileName);
            }

            if (!isset($node['file'])) {
                throw new RuntimeException('Missing `file` attribute in `violation` in ' . $fileName);
            }

            $methodName = null;

            if (isset($node['method']) && ((string) $node['method']) !== '') {
                $methodName = (string) $node['method'];
            }

            $baselineSet->addEntry(new ViolationBaseline((string) $node['rule'], (string) $node['file'], $methodName));
        }

        return $baselineSet;
    }

    /**
     * @throws RuntimeException
     */
    private static function loadXml(string $fileName): SimpleXMLElement
    {
        if (!is_readable($fileName)) {
            throw new RuntimeException('Unable to load the baseline file at: ' . $fileName);
        }

        $content = file_get_contents($fileName);

        $libxml = libxml_use_internal_errors(true);
        $xml = $content ? simplexml_load_string($content) : false;
        libxml_use_internal_errors($libxml);

        if (!$xml) {
            throw new RuntimeException('Unable to read xml from: ' . $fileName);
        }

        return $xml;
    }
}
