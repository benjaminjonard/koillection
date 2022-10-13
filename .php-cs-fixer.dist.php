<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/api')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/migrations')
    ->in(__DIR__ . '/tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'native_function_invocation' => true,
        'declare_strict_types' => true,
        'yoda_style' => false,
        'trailing_comma_in_multiline' => false,
        'php_unit_method_casing' => ['case' => 'snake_case']
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
