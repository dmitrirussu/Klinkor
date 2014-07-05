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
	private static $HASHED_PASSWORD = true;

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

		$nickname = Request::session()->getVar('nickname', false);
		$password = Request::session()->getVar('password', false);

		if ( empty($nickname) || empty($password) ) {

			return false;
		}

		return true;
	}

	/**
	 * Login user by nickname and his Password
	 * @param $nickname
	 * @param $password
	 * @param $remember
	 * @return bool
	 */
	public static function login($nickname, $password, $remember = false) {

		if ( empty($nickname) || empty($password) || !Login::verify($password, self::getHashedPassword())) {

			return false;
		}

		Request::session()->setVar('nickname', $nickname);
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

		Request::session()->destroy();
	}
} 