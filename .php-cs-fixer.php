<?php

/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.54.0|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PER-CS2.0' => true,
        'binary_operator_spaces' => true, // Going beyond PER CS v2
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                // Going beyond PER CS v2
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                // We do not want to order methods
//                'method_public',
//                'method_protected',
//                'method_private',
            ]
        ],
        'single_line_empty_body' => false, // Not adhering to PER CS v2
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => [
                // Not adhering to PER CS v2; we don't want trailing commas for arguments
//                'arguments',
                'arrays',
                'match',
                // Not adhering to PER CS v2; we don't want trailing commas for parameters
//                'parameters'
            ]
        ],
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => true,
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unused_imports' => true,
        // Using sorting algo "alpha" instead of "none" defined in PER CS v2
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'phpdoc_align' => true,
        'phpdoc_indent' => true,
        'phpdoc_order' => [
            'order' => [
                'param', // Input, thus first
                'return', // Output, thus second
                'throws', // Important exceptional situations
                'var', // For properties & inline type hints only
                'global', // Hints for properties & methods in classes
                'property', // Hints for properties & methods in classes
                'property-read', // Hints for properties & methods in classes
                'property-write', // Hints for properties & methods in classes
                'method', // Hints for properties & methods in classes
                'dataProvider', // For tests only, defines the input
                'covers', // For tests only, defines the coverage
                'author', // Normally comes before copyright
                'copyright', // Normally comes before license
                'license', // Should be after author and copyright
                'see', // Links & examples
                'link', // Links & examples
                'example', // Links & examples
                'internal', // Would be important, if present
                'api', // Category & packages
                'category', // Category & packages
                'package', // Category & packages
                'subpackage', // Category & packages
                'uses', // Usage
                'used-by', // Usage
                'source', // Source & version
                'version', // Source & version
                'since', // Source & version
                'filesource', // phpDocumentor specific
                'ignore', // phpDocumentor specific
                'deprecated', // Should be to the end to better notice them
                'todo', // Should be last to better notice them
            ],
        ],
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'phpdoc_var_annotation_correct_order' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude('vendor')
        ->in([
            __DIR__ . '/src/main/php/PHPMD',
            __DIR__ . '/src/test/php',
        ])
    );
