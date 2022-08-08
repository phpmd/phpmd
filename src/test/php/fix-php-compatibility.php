<?php

$replacements = array(
    /**
     * Patch phpunit/phpunit-mock-objects Generator.php file to not create double nullable tokens: `??`
     */
    __DIR__ . '/../../../vendor/phpunit/phpunit-mock-objects/src/Framework/MockObject/Generator.php' => array(
        array(
            "if (version_compare(PHP_VERSION, '7.1', '>=') && " .
            "\$parameter->allowsNull() && !\$parameter->isVariadic()) {",
            "if (version_compare(PHP_VERSION, '7.1', '>=') && version_compare(PHP_VERSION, '8.0', '<') && " .
            "\$parameter->allowsNull() && !\$parameter->isVariadic()) {",
        ),
    ),
    __DIR__ . '/../../../vendor/phpunit/phpunit/src/Util/Configuration.php' => array(
        array(
            'final private function',
            'private function',
        ),
        array(
            '$target = &$GLOBALS;',
            '',
        ),
    ),
    __DIR__ . '/../../../vendor/phpunit/phpunit/src/Framework/Constraint.php' => array(
        array(
            'public function count()',
            "#[\\ReturnTypeWillChange]\npublic function count()",
            '#[\\ReturnTypeWillChange]',
        ),
    ),
    __DIR__ . '/../../../vendor/phpunit/php-token-stream/src/Token/Stream.php' => array(
        array(
            'public function offsetExists',
            "#[\\ReturnTypeWillChange]\npublic function offsetExists",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function offsetGet',
            "#[\\ReturnTypeWillChange]\npublic function offsetGet",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function offsetSet',
            "#[\\ReturnTypeWillChange]\npublic function offsetSet",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function offsetUnset',
            "#[\\ReturnTypeWillChange]\npublic function offsetUnset",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function count',
            "#[\\ReturnTypeWillChange]\npublic function count",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function seek',
            "#[\\ReturnTypeWillChange]\npublic function seek",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function current',
            "#[\\ReturnTypeWillChange]\npublic function current",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function next',
            "#[\\ReturnTypeWillChange]\npublic function next",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function key',
            "#[\\ReturnTypeWillChange]\npublic function key",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function valid',
            "#[\\ReturnTypeWillChange]\npublic function valid",
            '#[\\ReturnTypeWillChange]',
        ),
        array(
            'public function rewind',
            "#[\\ReturnTypeWillChange]\npublic function rewind",
            '#[\\ReturnTypeWillChange]',
        ),
    ),
    __DIR__ . '/../../../vendor/phpunit/phpunit/src/Util/TestSuiteIterator.php' => array(
        array(
            'public function current',
            "#[\\ReturnTypeWillChange]\npublic function current",
            "#[\\ReturnTypeWillChange]\npublic function current",
        ),
        array(
            'public function next',
            "#[\\ReturnTypeWillChange]\npublic function next",
            "#[\\ReturnTypeWillChange]\npublic function next",
        ),
        array(
            'public function key',
            "#[\\ReturnTypeWillChange]\npublic function key",
            "#[\\ReturnTypeWillChange]\npublic function key",
        ),
        array(
            'public function valid',
            "#[\\ReturnTypeWillChange]\npublic function valid",
            "#[\\ReturnTypeWillChange]\npublic function valid",
        ),
        array(
            'public function rewind',
            "#[\\ReturnTypeWillChange]\npublic function rewind",
            "#[\\ReturnTypeWillChange]\npublic function rewind",
        ),
        array(
            'public function getChildren',
            "#[\\ReturnTypeWillChange]\npublic function getChildren",
            "#[\\ReturnTypeWillChange]\npublic function getChildren",
        ),
        array(
            'public function hasChildren',
            "#[\\ReturnTypeWillChange]\npublic function hasChildren",
            "#[\\ReturnTypeWillChange]\npublic function hasChildren",
        ),
    ),
);

foreach ($replacements as $file => $patterns) {
    echo "$file: ";

    if (!file_exists($file)) {
        echo "File not found.\n";

        continue;
    }

    foreach ($patterns as $replacement) {
        list($from, $to, $exclude) = array_pad($replacement, 3, null);

        $contents = @file_get_contents($file) ?: '';

        if ($exclude && strpos($contents, $exclude) !== false) {
            echo "Already changed.\n";

            continue;
        }

        $newContents = str_replace($from, $to, $contents);

        if ($newContents !== $contents) {
            file_put_contents($file, $newContents);
            echo "Content changed.\n";

            continue;
        }

        echo "Replace pattern not found.\n";
    }
}
