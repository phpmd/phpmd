<?php

/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.54.0|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        'array_indentation' => true,
        'binary_operator_spaces' => true,
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_between_import_groups' => true,
        'blank_lines_before_namespace' => true,
        'braces_position' => ['allow_single_line_empty_anonymous_classes' => true],
        'cast_spaces' => true,
        'class_definition' => ['inline_constructor_arguments' => false, 'space_before_parenthesis' => true],
        'compact_nullable_type_declaration' => true,
        'concat_space' => ['spacing' => 'one'],
        'constant_case' => true,
        'control_structure_braces' => true,
        'control_structure_continuation_position' => true,
        'declare_equal_normalize' => true,
        'elseif' => true,
        'encoding' => true,
        'full_opening_tag' => true,
        'function_declaration' => ['closure_fn_spacing' => 'none'],
        'indentation_type' => true,
        'line_ending' => true,
        'lowercase_cast' => true,
        'lowercase_keywords' => true,
        'lowercase_static_reference' => true,
        'method_argument_space' => true,
        'new_with_parentheses' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_break_comment' => true,
        'no_closing_tag' => true,
        'no_multiple_statements_per_line' => true,
        'no_space_around_double_colon' => true,
        'no_spaces_after_function_name' => true,
        'no_trailing_whitespace' => true,
        'no_trailing_whitespace_in_comment' => true,
        'no_whitespace_in_blank_line' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
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
        'return_type_declaration' => true,
        'short_scalar_cast' => true,
        'single_blank_line_at_eof' => true,
        'single_class_element_per_statement' => ['elements' => ['property']],
        'single_import_per_statement' => ['group_to_single_imports' => false],
        'single_line_empty_body' => false, // Not adhering to PER CS v2
        'single_trait_insert_per_statement' => true,
        'spaces_inside_parentheses' => true,
        'statement_indentation' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => [
                // We don't want trailing commas for arguments
//                'arguments',
                'arrays',
                'match',
                // We don't want trailing commas for parameters
//                'parameters'
            ]
        ],
        'unary_operator_spaces' => ['only_dec_inc' => true],
        'visibility_required' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => true,
        'no_empty_phpdoc' => true,
        'no_leading_import_slash' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
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
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'phpdoc_var_annotation_correct_order' => true,
        'single_line_after_imports' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude('vendor')
        ->in(__DIR__ . '/src/main/php/PHPMD')
        ->in(__DIR__ . '/src/test/php/')
    );
