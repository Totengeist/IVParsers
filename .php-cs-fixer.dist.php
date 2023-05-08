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
        'array_syntax' => array('syntax' => 'short'),
        'binary_operator_spaces' => false,
        'concat_space' => array('spacing' => 'one'),
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'increment_style' => 'post',
        'increment_style' => false,
        'simplified_null_return' => false,
        'trailing_comma_in_multiline' => false,
        'yoda_style' => false,
        'indentation_type' => true,
        'line_ending' => false,

        'phpdoc_no_package' => false,
        'phpdoc_order' => true,
        'phpdoc_summary' => true,
        'phpdoc_types_order' => array('null_adjustment' => 'none', 'sort_algorithm' => 'none'),
        
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'same'
        ],
    ))
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setFinder($finder)
;

return $config;
