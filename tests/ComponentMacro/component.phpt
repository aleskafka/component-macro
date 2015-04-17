<?php

/**
 * Test: Latte\Engine: {include file}
 */

require __DIR__ . '/../bootstrap.php';

$latte = new Latte\Engine;
$latte->setTempDirectory(TEMP_DIR);
ComponentMacro::install($latte, __DIR__ . '/components');

matchFile(
	__DIR__ . '/expected/component.html',
	$latte->renderToString(__DIR__ . '/templates/component.latte')
);

matchFile(
	__DIR__ . '/expected/component.multi.html',
	$latte->renderToString(__DIR__ . '/templates/component.latte', ['image' => 'test.jpg', 'navigation' => ['John']])
);
