<?php

$finder = new PhpCsFixer\Finder();
$config = new PhpCsFixer\Config('json-schema');
$finder->in(__DIR__);

$config
    ->setRules(array(
        // default
        '@PSR2' => true,
        '@Symfony' => true,
        // additionally
        'array_syntax' => array('syntax' => 'long'),
        'binary_operator_spaces' => false,
        'concat_space' => array('spacing' => 'one'),
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'increment_style' => ['style' => 'post'],
        'simplified_null_return' => false,
        'trailing_comma_in_multiline' => false,
        'yoda_style' => false,
        'indentation_type' => true,

        'phpdoc_no_package' => false,
        'phpdoc_order' => true,
        'phpdoc_summary' => true,
        'phpdoc_types_order' => array('null_adjustment' => 'always_last', 'sort_algorithm' => 'none'),

        'curly_braces_position' => [
            'control_structures_opening_brace' => 'same_line',
            'functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line',
            'anonymous_classes_opening_brace' => 'same_line',
            'allow_single_line_empty_anonymous_classes' => true,
            'allow_single_line_anonymous_functions' => true,
        ],

        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
        ]
    ))
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setFinder($finder)
;

return $config;
