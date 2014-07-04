<?php
/**
 * Created by Dumitru Russu.
 * Date: 04.05.2014
 * Time: 15:02
 * Request${NAME} 
 */

namespace AppLauncher\Action;


class Request {

	private static $session;

	public function __construct() {}
	private function __clone() {}

	public static function isGet() {

		return (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] === 'GET' : false);
	}

	public static function isPost() {

		return (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] === 'POST' : false);
	}

	public static function get($name, $type = 'null', $defaultValue = null) {

		if ( !isset($_GET[$name]) ) {

			$_GET[$name] = $defaultValue;
		}

		$castingValue = new CastingValue($_GET[$name], $type);

		return $castingValue->getValue();
	}

	public static function getAllGetData() {

		return $_GET;
	}

	public function getAllPostData() {

		return $_POST;
	}

	public function getAllRequestData() {

		return $_REQUEST;
	}

	public function getAllFileData() {

		return $_FILES;
	}

	public static function post($name, $type = 'null', $defaultValue = null) {
		if ( !isset($_POST[$name]) ) {

			$_POST[$name] = $defaultValue;
		}

		$castingValue = new CastingValue($_POST[$name], $type);

		return $castingValue->getValue();
	}

	public static function file($name, array $default = array()) {
		if ( !isset($_FILES[$name]) ) {

			$_FILES[$name] = $default;
		}

		return $_FILES[$name];
	}

	public static function cookie($name, $type = 'null', $defaultValue = null) {

		if ( !isset($_COOKIE[$name]) ) {

			$_COOKIE[$name] = $defaultValue;
		}

		$castingValue = new CastingValue($defaultValue, $type);

		return $castingValue->getValue();
	}

	/**
	 * Session life time 30 minutes
	 * @param string $name
	 * @return Session
	 */
	public static function session($name = 'global') {

		if ( empty(self::$session) ) {

			self::$session = new Session($name);
		}

		return self::$session;
	}

	/**
	 * Redirect
	 * @param $url
	 */
	public static function redirect($url) {

		header('Location: '. $url);
		exit;
	}
}

class RequestException extends \Exception {

}