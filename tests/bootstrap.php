<?php

// The Nette Tester command-line runner can be
// invoked through the command: ../vendor/bin/tester .
if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}

// configure environment
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

// create temporary directory
define('TEMP_DIR', __DIR__ . '/tmp/' . getmypid());

@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
Tester\Helpers::purge(TEMP_DIR);


function matchFile($file, $actual)
{
	$pattern = @file_get_contents($file);
	if ($pattern === FALSE) {
		throw new \Exception("Unable to read file '$file'.");
	}

	Tester\Assert::match(trimFile($pattern), trimFile($actual));
}


function trimFile($content)
{
	return trim(preg_replace("#^[\t\s]+#m", "", $content));
}
