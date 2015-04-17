<?php

/**
 * Test: Latte\Engine: {include file}
 */

require __DIR__ . '/../bootstrap.php';

$latte = new Latte\Engine;
$latte->setTempDirectory(TEMP_DIR);
ComponentMacro::install($latte, __DIR__ . '/components');

matchFile(
	__DIR__ . '/expected/child.key.html',
	$latte->renderToString(__DIR__ . '/templates/child.key.latte', ['title' => 'Hello', 'small' => 'world'])
);


matchFile(
	__DIR__ . '/expected/child.double.html',
	$latte->renderToString(__DIR__ . '/templates/child.double.latte', ['title' => 'Hello', 'navigation' => ['John']])
);
