<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'tests')
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'src')
    ->append(['.php_cs']);

$rules = [
    '@Symfony'                   => true,
    'phpdoc_no_empty_return'     => false,
    'phpdoc_summary'             => false,
    'no_superfluous_phpdoc_tags' => false,
    'phpdoc_separation'          => false,
    'phpdoc_trim'                => false,
    'phpdoc_align'               => false,
    'array_syntax'               => ['syntax' => 'short'],
    'yoda_style'                 => true,
    'binary_operator_spaces'     => false,
    'concat_space'               => ['spacing' => 'one'],
    'not_operator_with_space'    => false,
];

$rules['increment_style'] = ['style' => 'post'];

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setRules($rules)
    ->setFinder($finder);
