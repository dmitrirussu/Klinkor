<?php
/**
 * Created by Dumitru Russu.
 * Date: 25.09.2015
 * Time: 12:52
 * AppLauncher\Action${NAME} 
 */

namespace AppLauncher\Action;


class Cookies {

	public function __construct() {

	}

	public function setVar($name, $value, $lifeTime, $domain = null) {
		if ( empty($name) ) {
			throw new \InvalidArgumentException('Missing var name of cookie');
		}

		$lifeTime = ($lifeTime ? $lifeTime : time()+60*60*24*30);
		$domain = ($domain ? $domain : $_SERVER['HTTP_HOST']);

		@setcookie($name, $value, $lifeTime,'/', $domain);
	}

	public function getVar($name, $default = null) {
		return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default);
	}

	public function unsetVat($name, $domain = null){
		$domain = ($domain ? $domain : $_SERVER['HTTP_HOST']);

		if(isset($_COOKIE[$name])) {
			setcookie($name, '', time()-3600);
			setcookie($name, '', time()-3600, '/', $domain);

			if ( isset($_COOKIE[$name]) ) {
				unset($_COOKIE[$name]);
			}

			return true;
		}
		return false;
	}

	public function destroyAll() {
		// unset cookies
		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				setcookie($name, '', time()-1000);
				setcookie($name, '', time()-1000, '/');
			}
		}
	}
}