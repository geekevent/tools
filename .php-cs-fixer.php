<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
    '@PSR12' => true,
    '@Symfony' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
    'declare_strict_types' => true,
])
    ->setFinder($finder)
    ;