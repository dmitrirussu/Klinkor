<?php
/**
 * Created by Dumitru Russu.
 * Date: 04.05.2014
 * Time: 15:02
 * Request${NAME} 
 */

namespace AppLauncher\Action;


use AppLauncher\Utils\MethodUtils;

class Request {

	private static $session;
	private static $cookies;

	public function __construct() {}
	private function __clone() {}

	public static function isGet() {

		return (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] === 'GET' : false);
	}

	public static function isPost() {

		return (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] === 'POST' : false);
	}

	public static function get($name, $type = 'null', $defaultValue = null) {

		if ( !isset($_GET[$name]) || $_GET[$name] === null ) {

			return $_GET[$name] = $defaultValue;
		}

		$castingValue = new CastingValue($_GET[$name], $type);

		return $castingValue->getValue();
	}

	public static function getAllGetData() {

		return $_GET;
	}


	/**
	 * Allow ajax if has HEADER 'X-Requested-With', 'XMLHttpRequest'
	 * @return bool
	 */
	public function isAjax() {
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		}
		return false;
	}


	/**
	 * Allow CRUL or any RESTfull APi request if hsd defined next HEADER 'X-Requested-With-Api', 'apirequest'
	 * @return bool
	 */
	public function isApiRequest() {
		return true;
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH_API']) ) {
			return true;
		}
		return false;
	}


	public function getAllPostData($stripTags = false) {
		if ( is_array($_POST) && $stripTags) {
			MethodUtils::strip_tags_array($_POST);
		}
		return $_POST;
	}

	public function getAllRequestData() {

		return $_REQUEST;
	}

	/**
	 * Get input Data is recommended to use for REST Frontend Apps using BackBoneJS or AngularJS
	 * @return mixed|string
	 */
	public function getInputData($fetchAssoc = false, $deep = 512, $options = 0) {
		$inputData = file_get_contents('php://input');

		if ( $inputData = @json_decode($inputData, $fetchAssoc, $deep, $options) ) {
			return $inputData;
		}

		return $inputData;
	}

	/**
	 * @param null $file
	 * @return null
	 */
	public function getAllFileData($file = null) {
		if($file) {
			return isset($_FILES[$file]) ? $_FILES[$file] : null;
		}

		return $_FILES;
	}

	public static function post($name, $type = 'null', $defaultValue = null, $stripTags = true) {
		if ( !isset($_POST[$name]) || $_POST[$name] === null ) {

			return $_POST[$name] = $defaultValue;
		}

		$postData = $_POST[$name];

		if ( $type == 'array' && is_array($postData) && $stripTags) {
			MethodUtils::strip_tags_array($postData);
		}

		$castingValue = new CastingValue($postData, $type);

		return $castingValue->getValue();
	}

	public static function request($name, $type = 'null', $defaultValue = null, $stripTags = true) {
		if ( !isset($_REQUEST[$name]) || $_POST[$name] === null ) {

			return $_REQUEST[$name] = $defaultValue;
		}

		$postData = $_REQUEST[$name];

		if ( $type == 'array' && is_array($postData) && $stripTags) {
			MethodUtils::strip_tags_array($postData);
		}

		$castingValue = new CastingValue($postData, $type);

		return $castingValue->getValue();
	}

	public static function file($name, array $default = array()) {
		if ( !isset($_FILES[$name]) ) {

			$_FILES[$name] = $default;
		}

		return $_FILES[$name];
	}

	public static function cookie() {

		if ( empty(self::$cookies) ) {
			return self::$cookies = new Cookies();
		}

		return self::$cookies;
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
		self::$session->setSessionName($name);

		return self::$session;
	}

	/**
	 * Redirect
	 * @param $url
	 */
	public static function redirect($url, $https = false, $local = true) {
		$hostName = '';
		if ( $local ) {
			$hostName = ($https ? 'https://' : 'http://') .$_SERVER['HTTP_HOST'].'/';
		}

		header('Location: '. $hostName.ltrim($url, '/'));
		exit;
	}
}

class RequestException extends \Exception {

}