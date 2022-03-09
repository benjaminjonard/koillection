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
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
