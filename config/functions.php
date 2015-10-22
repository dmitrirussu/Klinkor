<?php

//set default time zone
//date_default_timezone_set('America/New_York');

//running load class
spl_autoload_register('autoload');

/**
 * Autoload Classes
 * @param $className
 */
function autoload($className) {
	$className = trim(str_replace('\\', '/', $className), '/');


	if ( @file_exists(dirname(__DIR__).'/'.$className .'.php') ) {
		require_once dirname(__DIR__).'/'.$className .'.php';
	}
	elseif( @file_exists(dirname(__DIR__).'/libs/'.$className.'.php') ) {

		require_once dirname(__DIR__).'/libs/'.$className .'.php';
	}
	elseif( @file_exists(dirname(__DIR__).'/app/'.$className.'.php') ) {

		require_once(dirname(__DIR__).'/app/'.$className.'.php');
	}
}


if (get_magic_quotes_gpc()) {
	function magicQuotes_awStripslashes(&$value, $key) {$value = stripslashes($value);}
	$gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);

	array_walk_recursive($gpc, 'magicQuotes_awStripslashes');
}
