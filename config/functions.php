<?php

spl_autoload_register('autoload');

function autoload($className) {

	$className = str_replace('\\', '/', $className);

	if ( @include_once dirname(__DIR__).$className .'.php' ) {

		require_once dirname(__DIR__).$className .'.php';
	}
	elseif( @include_once(dirname(__DIR__).'\\libs\\'.$className.'.php') ) {

		require_once dirname(__DIR__).'\\libs\\'.$className .'.php';
	}
	elseif( @include_once(dirname(__DIR__).'\\app\\'.$className.'.php') ) {

		require_once(dirname(__DIR__).'\\app\\'.$className.'.php');
	}
}