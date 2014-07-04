<?php
/**
 * Created by Dumitru Russu.
 * Date: 04.07.2014
 * Time: 09:49
 * AppLauncher\Secure${NAME} 
 */

namespace AppLauncher\Secure;



class Login {


	private function __construct() {}
	private function __clone() {}

	/**
	 * Get Generated Password Hash
	 * @param $password
	 * @return string
	 */
	public static function getGeneratedPasswordHash($password) {
		if (self::checkIsAvailableCryptBlowFish()) {

			$salt = '$2y$11$' . substr(md5(uniqid(mt_rand(), true)), 0, 22);
			return crypt($password, $salt);
		}

		return md5($password);
	}

	/**
	 * Verify Password
	 * @param $password
	 * @param $hashedPassword
	 * @return bool
	 */
	public static function verify($password, $hashedPassword) {
		if (self::checkIsAvailableCryptBlowFish()) {

			return crypt($password, $hashedPassword) == $hashedPassword;
		}

		return md5(trim($password)) == $hashedPassword;
	}

	/**
	 * Check is Available Crypt BlowFish
	 * @return bool
	 */
	private static function checkIsAvailableCryptBlowFish() {
		if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
			return true;
		}

		return false;
	}
} 