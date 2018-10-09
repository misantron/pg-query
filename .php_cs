<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'trailing_comma_in_multiline_array' => true,
        'linebreak_after_opening_tag' => true,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_no_package' => true,
        'cast_spaces' => ['space' => 'none'],
    ]);
