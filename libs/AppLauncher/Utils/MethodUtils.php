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

	/**
	 * Get Date
	 * @param null $strTime
	 * @param string $dateFormat
	 * @return string
	 */
	public static function dateFormat($strTime = null, $dateFormat = 'm-d-Y') {
		if ( empty($strTime) ) {
			$strTime = time();
		}

		$date = new \DateTime($strTime);
		return $date->format($dateFormat);
	}


	/**
	 * Pagination method
	 * @param $totalNumberOfPage
	 * @param int $currentPage
	 * @param int $maxLengthBeforeCurrentPage
	 * @param int $maxLengthAfterCurrentPage
	 * @return array($pages, $previousPage, $nextPage)
	 */
	public static function doPagination($totalNumberOfPage, $currentPage = 1,
										$maxLengthBeforeCurrentPage = 1, $maxLengthAfterCurrentPage = 5) {

		if ( is_array($totalNumberOfPage) ) {
			$totalNumberOfPage = count($totalNumberOfPage);
		}

		$totalNumberOfPage = (int)$totalNumberOfPage;
		$totalPages = range(1, $totalNumberOfPage);
		$currentPage = array_search($currentPage, $totalPages);
		$currentPage = $totalPages[$currentPage];

		$startForm = (($currentPage - $maxLengthBeforeCurrentPage) < 1 ? 1 : $currentPage - $maxLengthBeforeCurrentPage);
		$endOfPage = ($currentPage + $maxLengthAfterCurrentPage > $totalNumberOfPage ? $totalNumberOfPage : $currentPage + $maxLengthAfterCurrentPage);

		$pages = range($startForm, $endOfPage);

		$nextPage = (array_search($currentPage + 1, $totalPages) !== false ? $currentPage + 1 : $currentPage);
		$previousPage = (array_search($currentPage - 1, $totalPages) !== false ? $currentPage - 1 : $currentPage);

		return array($pages, $previousPage, $nextPage);
	}

	/**
	 * @param $email
	 * @return mixed
	 */
	public static function checkEmail($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}


	public static function writeIniFile($assoc_arr, $path, $has_sections)
	{
		$content = '';

		if (!$handle = fopen($path, 'w'))
			return FALSE;

		self::_write_ini_file_r($content, $assoc_arr, $has_sections);

		if (!fwrite($handle, $content))
			return FALSE;

		fclose($handle);
		return TRUE;
	}

	private static function _write_ini_file_r(&$content, $assoc_arr, $has_sections)
	{
		foreach ($assoc_arr as $key => $val) {
			if (is_array($val)) {
				if($has_sections) {
					$content .= "[$key]\n";
					self::_write_ini_file_r($content, $val, false);
				} else {
					foreach($val as $iKey => $iVal) {
						if (is_int($iKey))
							$content .= $key ."[] = '$iVal'\n";
						else
							$content .= $key ."[$iKey] = '$iVal'\n";
					}
				}
			} else {
				$content .= "$key = '$val'\n";
			}
		}
	}

	public static function encodeId($id) {
		return 'CT'.base_convert($id, 10, 36);
	}

	public static function decodeId($id) {
		return base_convert(substr($id, 2), 36, 10);
	}
} 