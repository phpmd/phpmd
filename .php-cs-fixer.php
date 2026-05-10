<?php

/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.54.0|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setParallelConfig(\PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PhpCsFixer' => true, // Gets overwritten by rules below
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
                //'method_public',
                //'method_protected',
                //'method_private',
            ]
        ],
        'single_line_empty_body' => false, // Not adhering to PER CS v2
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => [
                // Not adhering to PER CS v2; we don't want trailing commas for arguments
                //'arguments',
                'arrays',
                'match',
                // Not adhering to PER CS v2; we don't want trailing commas for parameters
                //'parameters'
            ]
        ],
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => true,
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unused_imports' => true,
        // Using sorting algo "alpha" instead of "none" defined in PER CS v2
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
                'SuppressWarnings', // PHPMD specific (Current format "@SuppressWarnings(*)" not supported)
                'phpcsSuppress', // PHPCS specific
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

        /*
         * PHP CS Fixer rules
         */

        // Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.
        'align_multiline_comment' => true,
        // PHP arrays should be declared using the configured syntax.
        'array_syntax' => true,
        // Converts backtick operators to `shell_exec` calls.
        'backtick_to_shell_exec' => true,
        // An empty line feed must precede any configured statement.
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'case',
                'continue',
                'declare',
                'default',
                'exit',
                'goto',
                'include',
                'include_once',
                'phpdoc',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'yield',
                'yield_from'
            ]
        ],
        // Class, trait and interface elements must be separated with one or none blank line.
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        // When referencing an internal class it must be written using the correct casing.
        'class_reference_name_casing' => true,
        // Namespace must not contain spacing, comments or PHPDoc.
        'clean_namespace' => true,
        // Using `isset($var) &&` multiple times should be done in one call.
        'combine_consecutive_issets' => true,
        // Calling `unset` on multiple items should be done in one call.
        'combine_consecutive_unsets' => true,
        // There must not be spaces around `declare` statement parentheses.
        'declare_parentheses' => true,
        // Replaces short-echo `<?=` with long format `<?php echo`/`<?php print` syntax, or vice-versa.
        'echo_tag_syntax' => ['format' => 'short', 'shorten_simple_statements_only' => true ],
        // Empty loop-body must be in configured style.
        'empty_loop_body' => true,
        // Empty loop-condition must be in configured style.
        'empty_loop_condition' => true,
        // Add curly braces to indirect variables to make them clear to understand. Requires PHP >= 7.0.
        'explicit_indirect_variable' => true,
        // Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.
        'explicit_string_variable' => false,
        // Renames PHPDoc tags.
        'general_phpdoc_tag_rename' => ['replacements' => ['inheritDocs' => 'inheritDoc']],
        // Convert `heredoc` to `nowdoc` where possible.
        'heredoc_to_nowdoc' => true,
        // Include/Require and file path should be divided with a single space. File path should not be placed within parentheses.
        'include' => true,
        // Pre- or post-increment and decrement operators should be used if possible.
        'increment_style' => false, // TODO Arguable
        // Integer literals must be in correct case.
        'integer_literal_case' => true,
        // Lambda must not import variables it doesn't use.
        'lambda_not_used_import' => true,
        // Ensure there is no code on the same line as the PHP open tag.
        'linebreak_after_opening_tag' => true,
        // Magic constants should be referred to using the correct casing.
        'magic_constant_casing' => true,
        // Magic method definitions and calls must be using the correct casing.
        'magic_method_casing' => true,
        // Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.
        'method_chaining_indentation' => true,
        // DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.
        'multiline_comment_opening_closing' => true,
        // Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
        'multiline_whitespace_before_semicolons' => false,
        // Function defined by PHP should be called using the correct casing.
        'native_function_casing' => true,
        // Native type declarations should be used in the correct case.
        'native_type_declaration_casing' => true,
        // Master language constructs shall be used instead of aliases.
        'no_alias_language_construct_call' => true,
        // Replace control structure alternative syntax to use braces.
        'no_alternative_syntax' => true,
        // There should not be a binary flag before strings.
        'no_binary_string' => true,
        // There should not be blank lines between docblock and the documented element.
        'no_blank_lines_after_phpdoc' => true,
        // There should not be any empty comments.
        'no_empty_comment' => true,
        // Remove useless (semicolon) statements.
        'no_empty_statement' => true,
        // Removes extra blank lines and/or blank lines following configuration.
        'no_extra_blank_lines' => [
            'tokens' => [
                'attribute',
                'break',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use'
            ]
        ],
        // The namespace declaration line shouldn't contain leading whitespace.
        'no_leading_namespace_whitespace' => true,
        // Either language construct `print` or `echo` should be used.
        'no_mixed_echo_print' => true,
        // Operator `=>` should not be surrounded by multi-line whitespaces.
        'no_multiline_whitespace_around_double_arrow' => true,
        // Properties MUST not be explicitly initialized with `null` except when they have a type declaration (PHP 7.4).
        'no_null_property_initialization' => false, // TODO Arguable, could remove info about nullability
        // Short cast `bool` using double exclamation mark should not be used.
        'no_short_bool_cast' => true,
        // Single-line whitespace before closing semicolon are prohibited.
        'no_singleline_whitespace_before_semicolons' => true,
        // There MUST NOT be spaces around offset braces.
        'no_spaces_around_offset' => true,
        // Replaces superfluous `elseif` with `if`.
        'no_superfluous_elseif' => true,
        // If a list of values separated by a comma is contained on a single line, then the last item MUST NOT have a trailing comma.
        'no_trailing_comma_in_singleline' => true,
        // Removes unneeded braces that are superfluous and aren't part of a control structure's body.
        'no_unneeded_braces' => ['namespaces' => true],
        // Removes unneeded parentheses around control statements.
        'no_unneeded_control_parentheses' => [
            'statements' => [
                'break',
                'clone',
                'continue',
                'echo_print',
                //'negative_instanceof',
                'others',
                //'return',
                'switch_case',
                'yield',
                'yield_from'
            ]
        ],
        // Imports should not be aliased as the same name.
        'no_unneeded_import_alias' => true,
        // Variables must be set `null` instead of using `(unset)` casting.
        'no_unset_cast' => true,
        // There should not be useless concat operations.
        'no_useless_concat_operator' => true,
        // There should not be useless `else` cases.
        'no_useless_else' => true,
        // There should not be useless Null-safe operator `?->` used.
        'no_useless_nullsafe_operator' => true,
        // There should not be an empty `return` statement at the end of a function.
        'no_useless_return' => true,
        // In array declaration, there MUST NOT be a whitespace before each comma.
        'no_whitespace_before_comma_in_array' => ['after_heredoc' => true],
        // Array index should always be written by using square braces.
        'normalize_index_brace' => true,
        // Nullable single type declaration should be standardised using configured syntax.
        'nullable_type_declaration' => true,
        // Adds or removes `?` before single type declarations or `|null` at the end of union types when parameters have a default `null` value.
        'nullable_type_declaration_for_default_null_value' => true,
        // There should not be space before or after object operators `->` and `?->`.
        'object_operator_without_whitespace' => true,
        // Operators - when multiline - must always be at the beginning or at the end of the line.
        // TODO Decide whether to put boolean operators at the end or the beginning. Either way lead to 10 changed files.
        'operator_linebreak' => false,
        //'operator_linebreak' => ['only_booleans' => true, 'position' => 'beginning'],
        //'operator_linebreak' => ['only_booleans' => true, 'position' => 'end'],
        // Sort union types and intersection types using configured order.
        'ordered_types' => true,
        // PHPUnit annotations should be a FQCNs including a root namespace.
        'php_unit_fqcn_annotation' => true,
        // All PHPUnit test classes should be marked as internal.
        'php_unit_internal_class' => false,
        // Enforce camel (or snake) case for PHPUnit test methods, following configuration.
        'php_unit_method_casing' => true,
        // Adds a default `@coversNothing` annotation to PHPUnit test classes that have no `@covers*` annotation.
        'php_unit_test_class_requires_covers' => false, // TODO We should enforce the opposite instead but not possible with PHP CS Fixer
        // PHPDoc should contain `@param` for all params.
        'phpdoc_add_missing_param_annotation' => false, // Can't be enabled due to "no_superfluous_phpdoc_tags" rule
        // All items of the given PHPDoc tags must be either left-aligned or (by default) aligned vertically.
        'phpdoc_align' => false,
        // PHPDoc annotation descriptions should not be a sentence.
        'phpdoc_annotation_without_dot' => false, // Also reduces the Casing of the First word of the description.
        // Fixes PHPDoc inline tags.
        'phpdoc_inline_tag_normalizer' => true,
        // `@access` annotations should be omitted from PHPDoc.
        'phpdoc_no_access' => true,
        // No alias PHPDoc tags should be used.
        'phpdoc_no_alias_tag' => false, // Changes @link to @see
        // `@return void` and `@return null` annotations should be omitted from PHPDoc.
        'phpdoc_no_empty_return' => false, // Removes a lot of return type hints that could be useful
        // `@package` and `@subpackage` annotations should be omitted from PHPDoc.
        'phpdoc_no_package' => true,
        // Classy that does not inherit must not have `@inheritdoc` tags.
        'phpdoc_no_useless_inheritdoc' => true,
        // Order PHPDoc tags by value.
        'phpdoc_order_by_value' => true,
        // Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other. Annotations of a different type are separated by a single blank line.
        'phpdoc_separation' => false,
        // PHPDoc summary should end in either a full stop, exclamation mark, or question mark.
        'phpdoc_summary' => false,
        // Forces PHPDoc tags to be either regular annotations or inline.
        'phpdoc_tag_type' => false,
        // Docblocks should only be used on structural elements.
        'phpdoc_to_comment' => false,
        // The correct case must be used for standard PHP types in PHPDoc.
        'phpdoc_types' => true,
        // `@var` and `@type` annotations of classy properties should not contain the name.
        'phpdoc_var_without_name' => true,
        // Converts `protected` variables and methods to `private` where possible.
        'protected_to_private' => true, // TODO Arguable, not a fan of
        // Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.
        'return_assignment' => false, // Makes debugging more difficult
        // Inside an enum or `final`/anonymous class, `self` should be preferred over `static`.
        'self_static_accessor' => true,
        // Instructions must be terminated with a semicolon.
        'semicolon_after_instruction' => true,
        // Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (`${` to `{$`).
        'simple_to_complex_string_variable' => true,
        // Single-line comments must have proper spacing.
        'single_line_comment_spacing' => true, // TODO Preferable for non-code, not so for actual code
        // Single-line comments and multi-line comments with only one line of actual content should use the `//` syntax.
        'single_line_comment_style' => true,
        // Throwing exception must be done in single line.
        'single_line_throw' => false,
        // Convert double quotes to single quotes for simple strings.
        'single_quote' => true,
        // Ensures a single space after language constructs.
        'single_space_around_construct' => true,
        // Fix whitespace after a semicolon.
        'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
        // Increment and decrement operators should be used if possible.
        'standardize_increment' => true,
        // Replace all `<>` with `!=`.
        'standardize_not_equals' => true,
        // Handles implicit backslashes in strings and heredocs. Depending on the chosen strategy, it can escape implicit backslashes to ease the understanding of which are special chars interpreted by PHP and which not (`escape`), or it can remove these additional backslashes if you find them superfluous (`unescape`). You can also leave them as-is using `ignore` strategy.
        'string_implicit_backslashes' => false,
        // Switch case must not be ended with `continue` but with `break`.
        'switch_continue_to_break' => true,
        // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
        'trim_array_spaces' => true,
        // Ensure single space between a variable and its type declaration in function arguments and properties.
        'type_declaration_spaces' => true,
        // A single space or none should be around union type and intersection type operators.
        'types_spaces' => true,
        // In array declaration, there MUST be a whitespace after each comma.
        'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
        // Write conditions in Yoda style (`true`), non-Yoda style (`['equal' => false, 'identical' => false, 'less_and_greater' => false]`) or ignore those conditions (`null`) based on configuration.
        'yoda_style' => false,

        /*
         * Additional rules, mostly risky ones
         */

        '@PHPUnit100Migration:risky' => true,
        '@PER-CS2.0:risky' => true,
        '@PHP81Migration' => true,
        '@PHP80Migration:risky' => true,

        'declare_strict_types' => false, // Not yet
        'pow_to_exponentiation' => false, // Risky

        'array_push' => true, // Risky
        'comment_to_phpdoc' => true, // Risky
        'date_time_create_from_format_call' => true, // Risky
        'dir_constant' => true, // Risky
        'ereg_to_preg' => true, // Risky
        'fopen_flag_order' => true, // Risky
        'fopen_flags' => true, // Risky
        'function_to_constant' => true, // Risky
        'general_phpdoc_annotation_remove' => false, // We do not want to remove any in general
        'heredoc_closing_marker' => true,
        'is_null' => false, // Risky // Uses yoda-style
        'logical_operators' => true, // Risky
        'long_to_shorthand_operator' => true, // Risky
        'mb_str_functions' => false, // Risky, leads to test suite errors
        'modernize_types_casting' => true, // Risky
        'no_unset_on_property' => false, // Risky, unset and null is not the same in PHP 7.4+
        'no_useless_sprintf' => true, // Risky
        'ordered_interfaces' => true,
        'ordered_traits' => true, // Risky
        'php_unit_construct' => true, // Risky
        'php_unit_set_up_tear_down_visibility' => true, // Risky
        'php_unit_strict' => false, // Risky
        'php_unit_test_annotation' => true, // Risky
        'php_unit_test_case_static_method_calls' => true, // Risky
        'phpdoc_line_span' => ['const' => 'single','property' => 'single'],
        'phpdoc_param_order' => true,
        'phpdoc_tag_casing' => true,
        'regular_callable_call' => true, // Risky
        'self_accessor' => true, // Risky
        'set_type_to_cast' => true, // Risky
        'simplified_if_return' => false, // TODO Arguable, can make it less readable
        'strict_comparison' => true, // Risky
        'strict_param' => true, // Risky
        'string_length_to_empty' => true, // Risky
        'string_line_ending' => true, // Risky
        'ternary_to_elvis_operator' => true, // Risky
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests/php/')
    );
