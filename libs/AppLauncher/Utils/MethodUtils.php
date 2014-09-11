<?php
/**
 * Created by Dumitru Russu.
 * Date: 13.08.2014
 * Time: 11:22
 * AppLauncher\Utils${NAME} 
 */

namespace AppLauncher\Utils;


class MethodUtils {

	/**
	 * Generate Random String
	 * @param $length
	 * @return string
	 */
	public static function generateRandomString($length) {
		$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijclmnopqrstuvwxyz1223456789';
		$max = strlen($alphabet) - 1;
		$result = '';

		for ($i = 0; $i < $length; $i++) {
			$result .= $alphabet[mt_rand(0, $max)];
		}

		return $result;
	}
} 