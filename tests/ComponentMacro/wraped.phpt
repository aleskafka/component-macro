<?php

/**
 * Test: Latte\Engine: {include file}
 */

require __DIR__ . '/../bootstrap.php';

$latte = new Latte\Engine;
$latte->setTempDirectory(TEMP_DIR);
ComponentMacro::install($latte, __DIR__ . '/components');

matchFile(
	__DIR__ . '/expected/wraped.html',
	$latte->renderToString(__DIR__ . '/templates/navigation.wraped.latte', ['title' => 'Hello', 'navigation' => ['John']])
);

