<?php
/**
 * Created by Dumitru Russu.
 * Date: 05.07.2014
 * Time: 00:05
 * AppLauncher\Secure${NAME} 
 */

namespace AppLauncher\Secure;


use AppLauncher\Action\Request;

class User {


	private function __construct() {}
	private function __clone() {}

	/**
	 * @var bool
	 */
	private static $HASHED_PASSWORD = null;

	/**
	 * @param $hashedPassword
	 */
	public static function setHashedPassword($hashedPassword) {

		self::$HASHED_PASSWORD = $hashedPassword;
	}

	/**
	 * @return bool
	 */
	public static function getHashedPassword() {

		return self::$HASHED_PASSWORD;
	}

	/**
	 * @return bool
	 */
	public static function isLogged() {

		$email = Request::session()->getVar('email', false);
		$password = Request::session()->getVar('password', false);

		if ( empty($email) || empty($password) ) {

			return false;
		}

		return true;
	}

	/**
	 * Login user by nickname and his Password
	 * @param $email
	 * @param $password
	 * @param $remember
	 * @return bool
	 */
	public static function login($email, $password, $hashedPassword, $remember = false) {

		if ( empty($email) || empty($password) || !Login::verify($password, $hashedPassword)) {

			return false;
		}

		Request::session()->setVar('email', $email);
		Request::session()->setVar('password', $password);

		//Set is Remember Life Time until 31 days
		if ( $remember ) {
			Request::session()->setSessionLifeTime((3600 * 24) * 31);
		}

		return true;
	}

	/**
	 * Do Logout
	 */
	public static function logout() {

		Request::session()->destroyAll();
	}
} 