<?php

declare(strict_types=1);

use Niklan\PhpCsFixer\Fixer\NamingConventionFixer;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

require_once __DIR__ . '/php-cs-fixer/Fixer/AbstractFixer.php';
require_once __DIR__ . '/php-cs-fixer/Fixer/NamingConventionFixer.php';

$finder = Finder::create()
  ->in(__DIR__ . '/../app')
  ->name(['*.php', '*.module', '*.inc', '*.install', '*.theme'])
  ->exclude('fixtures');

return (new Config())
  ->setRiskyAllowed(TRUE)
  ->setParallelConfig(ParallelConfigFactory::detect())
  ->setIndent('  ')
  ->setFinder($finder)
  ->registerCustomFixers([
    new NamingConventionFixer(),
  ])
  ->setRules([
    // Custom naming fixer.
    'Niklan/naming_convention' => TRUE,

    // Strict types.
    'declare_strict_types' => TRUE,

    // Imports.
    'ordered_imports' => [
      'sort_algorithm' => 'alpha',
      'imports_order' => ['class', 'function', 'const'],
    ],
    'no_unused_imports' => TRUE,
    'no_leading_import_slash' => TRUE,
    'single_import_per_statement' => TRUE,
    'single_line_after_imports' => TRUE,

    // Namespace / opening.
    'blank_line_after_opening_tag' => TRUE,
    'blank_lines_before_namespace' => [
      'min_line_breaks' => 2,
      'max_line_breaks' => 2,
    ],

    // Native function/constant invocation (\array_map, \PHP_EOL).
    'native_function_invocation' => [
      'include' => ['@all'],
      'scope' => 'all',
      'strict' => FALSE,
    ],
    'native_constant_invocation' => [
      'fix_built_in' => TRUE,
      'include' => [],
      'scope' => 'all',
      'strict' => FALSE,
    ],

    // Casting.
    'short_scalar_cast' => TRUE,
    'lowercase_cast' => TRUE,
    'cast_spaces' => ['space' => 'single'],
    'modernize_types_casting' => TRUE,

    // Trailing commas.
    'trailing_comma_in_multiline' => [
      'elements' => ['arguments', 'arrays', 'match', 'parameters'],
    ],

    // Operators.
    'binary_operator_spaces' => ['default' => 'single_space'],
    'concat_space' => ['spacing' => 'one'],
    'ternary_operator_spaces' => TRUE,
    'unary_operator_spaces' => ['only_dec_inc' => FALSE],
    'object_operator_without_whitespace' => TRUE,
    'no_spaces_around_offset' => TRUE,
    'ternary_to_null_coalescing' => TRUE,

    // Type declarations.
    'return_type_declaration' => ['space_before' => 'none'],
    'nullable_type_declaration_for_default_null_value' => TRUE,
    'visibility_required' => [
      'elements' => ['method', 'property', 'const'],
    ],

    // Style.
    'yoda_style' => [
      'equal' => FALSE,
      'identical' => FALSE,
      'less_and_greater' => FALSE,
    ],
    'static_lambda' => TRUE,
    'array_syntax' => ['syntax' => 'short'],
    'list_syntax' => ['syntax' => 'short'],

    // Class structure.
    'ordered_class_elements' => [
      'order' => [
        'use_trait',
        'case',
        'constant_public',
        'constant_protected',
        'constant_private',
        'property_public',
        'property_public_static',
        'property_protected',
        'property_protected_static',
        'property_private',
        'property_private_static',
        'method:create',
        'construct',
        'destruct',
        'method:__invoke',
        'magic',
        'phpunit',
        'method_public',
        'method_public_abstract',
        'method_public_static',
        'method_protected',
        'method_protected_abstract',
        'method_protected_static',
        'method_private',
        'method_private_static',
      ],
    ],

    // Cleanup.
    'no_empty_statement' => TRUE,
    'no_empty_comment' => TRUE,
    'no_unneeded_control_parentheses' => TRUE,
    'no_unneeded_curly_braces' => ['namespaces' => TRUE],

    // Whitespace.
    'no_trailing_whitespace' => TRUE,
    'no_whitespace_in_blank_line' => TRUE,
    'single_blank_line_at_eof' => TRUE,

    // Functions.
    'function_declaration' => [
      'closure_function_spacing' => 'one',
      'closure_fn_spacing' => 'one',
    ],
    'space_after_semicolon' => [
      'remove_in_empty_for_expressions' => TRUE,
    ],

    // PHPDoc.
    'no_superfluous_phpdoc_tags' => [
      'allow_mixed' => TRUE,
      'remove_inheritdoc' => FALSE,
    ],
    'phpdoc_trim' => TRUE,
    'phpdoc_indent' => TRUE,
    'phpdoc_scalar' => TRUE,
    'phpdoc_types' => TRUE,
    'no_empty_phpdoc' => TRUE,

    // Encoding.
    'encoding' => TRUE,
    'full_opening_tag' => TRUE,
    'lowercase_keywords' => TRUE,
  ]);
