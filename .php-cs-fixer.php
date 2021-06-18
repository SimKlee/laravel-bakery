<?php
/**
 * @see https://mlocati.github.io/php-cs-fixer-configurator/#version:3.0|fixer:explicit_indirect_variable
 */

$finder = PhpCsFixer\Finder::create()
    ->exclude('somedir')
    ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
	'@PhpCsFixer' => true,
	'@PSR12' => true,
        'strict_param' => true,
	'array_syntax' => ['syntax' => 'short'],
	'declare_strict_types' => true,
	'elseif' => true,
	'explicit_indirect_variable' => true,
	'explicit_string_variable' => true,
	'global_namespace_import' => true,
    ])
    ->setFinder($finder)
;
