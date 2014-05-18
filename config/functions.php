<?php

/**
 * @param $className
 */
function __autoload( $className ) {

	$className = str_replace('\\', '/', $className);

	if ( @include_once $className .'.php' ) {

		require_once $className .'.php';
	}
	elseif( @include_once('libs\\'.$className.'.php') ) {

		require_once 'libs\\'.$className .'.php';
	}
	elseif( @include_once('app\\'.$className.'.php') ) {

		require_once('app\\'.$className.'.php');
	}
}