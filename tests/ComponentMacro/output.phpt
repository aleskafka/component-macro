<?php

/**
 * Test: Latte\Engine: {include file}
 */

require __DIR__ . '/../bootstrap.php';

$latte = new Latte\Engine;
$latte->setTempDirectory(TEMP_DIR);
ComponentMacro::install($latte, __DIR__ . '/components');

matchFile(
	__DIR__ . '/expected/output.html',
	$latte->renderToString(__DIR__ . '/templates/output.latte', ['title' => 'Hello', 'navigation' => ['John']])
);

