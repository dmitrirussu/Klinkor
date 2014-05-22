<?php


if (!is_file($autoloadFile = dirname(__DIR__) . '/config/functions.php')) {
	throw new \LogicException('Could not find autoload.php in vendor/');
}

include $autoloadFile;