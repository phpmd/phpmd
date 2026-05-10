<?php

return [
    'name' => 'Test Exclude Pattern',
    'description' => 'Test exclude-pattern in PHP',
    'exclude-pattern' => [
        '*sourceExcluded/*.php',
        '*sourceExcluded\\*.php',
    ],
    'rules' => [
        ['ref' => 'CyclomaticComplexity'],
    ],
];
