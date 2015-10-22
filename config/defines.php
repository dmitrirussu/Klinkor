<?php

define('PATH_APP', dirname(__DIR__). DIRECTORY_SEPARATOR . 'app/');
define('PATH_LIBS', dirname(__DIR__). DIRECTORY_SEPARATOR . 'libs/');
define('PATH_PRIVATE', dirname(__DIR__). DIRECTORY_SEPARATOR . 'private/');
define('PATH_PUBLIC', dirname(__DIR__). DIRECTORY_SEPARATOR . 'public/');
define('PATH_CONF', realpath(__DIR__). DIRECTORY_SEPARATOR);

define('APP_FOLDER', str_replace('\\', '', str_replace(str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']), '', str_replace('/', '\\', dirname(__DIR__).DIRECTORY_SEPARATOR))));

$hostName = '//localhost';

if ( isset($_SERVER['HTTP_HOST']) ) {
	$hostName = '//' .$_SERVER['HTTP_HOST'];
	if ( APP_FOLDER ) {
		$hostName = $hostName.'/'.APP_FOLDER;
	}
}

define('DOMAIN_RESOURCES', $hostName);
