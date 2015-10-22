<?php
/**
 * Created by Dumitru Russu.
 * Date: 17.05.2015
 * Time: 07:53
 * ${NAMESPACE}\${NAME} 
 */


if (version_compare(phpversion(), '5.3', '<')) {
	die('Your php version is less when 5.3');
}

if (!in_array('PDO', get_loaded_extensions())) {
	die('Missing PDO (mysql Driver)');
}

if (!in_array('pdo_mysql', get_loaded_extensions())) {
	die('Missing pdo_mysql (mysql Driver)');
}

if (!in_array('curl', get_loaded_extensions())) {
	die('Missing CURL php extension');
}

if (!in_array('zip', get_loaded_extensions())) {
	die('Missing ZIP php extension');
}

if (!in_array('json', get_loaded_extensions())) {
	die('Missing JSON php extension');
}

if ( !in_array('mod_rewrite', apache_get_modules()) ) {
	die('Missing Apache Module Mod-Rewrite');
}