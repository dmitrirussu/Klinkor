<?php
/**
 * Created by Dumitru Russu.
 * Date: 13.08.2014
 * Time: 11:22
 * AppLauncher\Utils${NAME} 
 */

namespace AppLauncher\Utils;


use BackOfficeApp\Models\PopaccountingPackage\Store\CheckMailbox;
use SoapClient\nusoap;

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
			$strTime = 'now';
		}

		$date = new \DateTime($strTime);
		return $date->format($dateFormat);
	}

	public static function numberFormat($number, $dec = 2, $toDecPoint = '.') {
		if ( is_string($number) ) {
			$number = str_replace(array(','), '.', str_replace(' ', '', $number));
		}

		return number_format($number, $dec, $toDecPoint, '');
	}

	public static function invoiceNumberFormat($number, $dec = 2, $toDecPoint = '.') {
		if ( is_string($number) ) {
			$number = str_replace(array(','), '.', str_replace(' ', '', $number));
		}

		return number_format($number, $dec, $toDecPoint, ' ');
	}

	public static function createPagination($numberOfItems, $currentPage = 1, $itemsPerPage = 5, $maxLengthBeforeCurrentPage = 1, $maxLengthAfterCurrentPage = 5) {
		$numberOfPages = ceil($numberOfItems / $itemsPerPage);
		return self::doPagination($numberOfPages, $currentPage, $maxLengthBeforeCurrentPage, $maxLengthAfterCurrentPage);
	}

	/**
	 * Pagination method
	 * @param $totalNumberOfPage
	 * @param int $currentPage
	 * @param int $maxLengthBeforeCurrentPage
	 * @param int $maxLengthAfterCurrentPage
	 * @deprecated
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
		$endOfPage = (($currentPage + $maxLengthAfterCurrentPage) > $totalNumberOfPage ? ($totalNumberOfPage == 0 ? 1 : $totalNumberOfPage) : $currentPage + $maxLengthAfterCurrentPage);

		$pages = range($startForm, $endOfPage);



		$nextPage = (array_search($currentPage + 1, $totalPages) !== false ? $currentPage + 1 : $currentPage);
		$previousPage = (array_search($currentPage - 1, $totalPages) !== false ? $currentPage - 1 : $currentPage);


		$firsPage = (($currentPage-$maxLengthBeforeCurrentPage) > 1 ? 1 : '');
		$lastPage = (($maxLengthAfterCurrentPage+$currentPage) < $totalNumberOfPage ? $totalNumberOfPage : '');


		return array($pages, $previousPage, $nextPage, $firsPage, $lastPage);
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

	/**
	 *
	 * @param $s
	 * @param $macros
	 * @return mixed
	 */
	public static function macrosReplace(&$s, $macros) {
		if ( $macros ) {
			foreach ($macros as $key => $value) {
				$key = strtoupper($key);
				$s = str_replace("[$key]", $value, $s);
			}
		}
		return $s;
	}

	/**
	 * Time Stamp to Mysql DateTime
	 * @param $strTime
	 * @return bool|string
	 */
	public static function timestampToMySqlDateTime($strTime){
		if ( empty($strTime) ) {
			return false;
		}

		return date('Y-m-d H:i:s', $strTime);
	}

	public static function encodeId($id) {
		return 'CT'.base_convert($id, 10, 36);
	}

	public static function decodeId($id) {
		return base_convert(substr($id, 2), 36, 10);
	}

	public static function checkNumber($faxNumber){
		return preg_match('/^\+[0-9]{5,}$/', $faxNumber);
	}

	/**
	 * Download File
	 * @param $fileName
	 * @return bool
	 */
	public static function downloadFile($fileName, $exportFileName = '', $destroyFile = false) {

		if ( empty($fileName) ) {
			return false;
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".($exportFileName ? $exportFileName : basename($fileName))."\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($fileName));
		@readfile($fileName);
		($destroyFile ? @unlink($fileName) : '');
		exit;
	}

	/**
	 * Strip Tags
	 * @param $text
	 * @return string
	 */
	public static function strip_tags($text) {

		$text = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $text);

		return strip_tags($text);
	}

	/**
	 * Strip tag Array
	 * @param $array
	 */
	public static function strip_tags_array(&$array) {

		if ( is_array($array) ) {
			foreach($array AS $key => &$value) {
				if ( is_array($value) ) {
					self::strip_tags_array($value);
				}
				else {
					$value = self::strip_tags($value);
				}
			}
		}

	}

	public static function getPDFNumberOfPages($pdfFile) {
		$pdfText = file_get_contents($pdfFile);
		$num = preg_match_all("/\/Page\W/", $pdfText, $dummy);
		return $num;
	}

	/**
	 * Returns a random generated key.
	 *
	 * @param int $len The key length
	 * @return string
	 */
	public static function generateRandomKey()
	{
		return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
	}

	public static function http_response_code($code = null){
		if (!function_exists('http_response_code')) {
			function http_response_code($code = NULL) {

				if ($code !== NULL) {

					switch ($code) {
						case 100: $text = 'Continue'; break;
						case 101: $text = 'Switching Protocols'; break;
						case 200: $text = 'OK'; break;
						case 201: $text = 'Created'; break;
						case 202: $text = 'Accepted'; break;
						case 203: $text = 'Non-Authoritative Information'; break;
						case 204: $text = 'No Content'; break;
						case 205: $text = 'Reset Content'; break;
						case 206: $text = 'Partial Content'; break;
						case 300: $text = 'Multiple Choices'; break;
						case 301: $text = 'Moved Permanently'; break;
						case 302: $text = 'Moved Temporarily'; break;
						case 303: $text = 'See Other'; break;
						case 304: $text = 'Not Modified'; break;
						case 305: $text = 'Use Proxy'; break;
						case 400: $text = 'Bad Request'; break;
						case 401: $text = 'Unauthorized'; break;
						case 402: $text = 'Payment Required'; break;
						case 403: $text = 'Forbidden'; break;
						case 404: $text = 'Not Found'; break;
						case 405: $text = 'Method Not Allowed'; break;
						case 406: $text = 'Not Acceptable'; break;
						case 407: $text = 'Proxy Authentication Required'; break;
						case 408: $text = 'Request Time-out'; break;
						case 409: $text = 'Conflict'; break;case 410: $text = 'Gone'; break;
						case 411: $text = 'Length Required'; break;
						case 412: $text = 'Precondition Failed'; break;
						case 413: $text = 'Request Entity Too Large'; break;
						case 414: $text = 'Request-URI Too Large'; break;
						case 415: $text = 'Unsupported Media Type'; break;
						case 500: $text = 'Internal Server Error'; break;
						case 501: $text = 'Not Implemented'; break;
						case 502: $text = 'Bad Gateway'; break;
						case 503: $text = 'Service Unavailable'; break;
						case 504: $text = 'Gateway Time-out'; break;
						case 505: $text = 'HTTP Version not supported'; break;
						default:
							exit('Unknown http status code "' . htmlentities($code) . '"');
							break;
					}

					$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

					header($protocol . ' ' . $code . ' ' . $text);

					$GLOBALS['http_response_code'] = $code;

				} else {

					$code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

				}

				return $code;

			}
		}

		return http_response_code($code);
	}

	/**
	 * Clean String
	 * @param $string
	 * @return mixed
	 */
	public static function cleanStringToEmailName($string) {
		return preg_replace(
			array( '#[\\s-]+#', '#[^A-Za-z0-9\. -]+#' ),
			array( '-', '' ),
			// the full cleanString() can be download from http://www.unexpectedit.com/php/php-clean-string-of-utf8-chars-convert-to-similar-ascii-char
			self::cleanString(urldecode($string))
		);
	}


	/**
	 * @param $docComment
	 * @return array|null
	 */
	public static function readControllerMethodTokensFromDocComment($docComment) {
		if ( empty($docComment) ) {
			return array();
		}

		preg_match_all('/@param\s+(?P<type>[a-zA-Z]+)\s+\\$(?P<name>[a-zA-Z_]+)\s+(?P<default_value>[^*]*)/s', $docComment, $out, PREG_SET_ORDER);
		array_walk($out, function(&$row){
			preg_match_all('/(?P<name>.*)(?<pattern>(:?"\/).*)|(?<value_x>.*)/', trim($row['default_value']), $outData, PREG_SET_ORDER);
			$outData = $outData[0];
			if ( isset($outData['value_x']) ) {
				$row['default_value'] = $outData['value_x'];
				$row['pattern'] = null;
			}
			elseif (!empty($outData['pattern']) && empty($outData['value'])) {
				$row['pattern'] = $outData['pattern'];
				$row['default_value'] = null;
			}
			elseif (!empty($outData['pattern']) && !empty($outData['value'])) {
				$row['pattern'] = trim($outData['pattern'], '"');
				$row['default_value'] = ($outData['value'] === 'null' ? null : $outData['value']);
			}
			else {
				$row['default_value'] = null;
				$row['pattern'] = null;
			}
		}, $out);

		return $out;
	}

	/**
	 * Replace Special Chars
	 * @param $text
	 * @return mixed
	 */
	public static function cleanString($text) {
		$utf8 = array(
			'/[áàâãªä]/u'   =>   'a',
			'/[ÁÀÂÃÄ]/u'    =>   'A',
			'/[ÍÌÎÏ]/u'     =>   'I',
			'/[íìîï]/u'     =>   'i',
			'/[éèêë]/u'     =>   'e',
			'/[ÉÈÊË]/u'     =>   'E',
			'/[óòôõºö]/u'   =>   'o',
			'/[ÓÒÔÕÖ]/u'    =>   'O',
			'/[úùûü]/u'     =>   'u',
			'/[ÚÙÛÜ]/u'     =>   'U',
			'/ç/'           =>   'c',
			'/Ç/'           =>   'C',
			'/ñ/'           =>   'n',
			'/Ñ/'           =>   'N',
			'/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
			'/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
			'/[“”«»„]/u'    =>   ' ', // Double quote
			'/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
		);
		return preg_replace(array_keys($utf8), array_values($utf8), $text);
	}

	/**
	 * Sanitize Mobile Number
	 * @param $number
	 * @return bool|string
	 */
	public static function sanitizeMobileNumber($number) {
		preg_match('/[0-9]+/', $number, $numberResult);
		$number = @end($numberResult);

		if ( empty($number) ) {
			return false;
		}

		$number = ltrim($number, '0');

		return $number;
	}

	/**
	 * Check Email is Valid By SMTP connection
	 * @param $email
	 * @param string $helo_domain
	 * @param string $from_address
	 * @param int $timeout
	 * @return mixed
	 */
	public static function checkEmailBySMTP($email, $helo_domain = 'www.popcompta.com', $from_address = "noreply@popcompta.com", $timeout = 5, $numberOfRetry = 3) {
		$returnStatuses = array(
			'OK' => 0,
			'Accepted' => 0,
			'Syntax' => 1,
			'DNS' => 2,
			'ConnectionTimeout' => 3,
			'ConnectionClosed' => 4,
			'NoMailbox' => 5);

		if ( !self::checkEmail($email) ) {
			return $returnStatuses["Syntax"];
		}

		$address_parts = explode('@',$email);
		$domain = $address_parts[1];
		$lastMX = '';

		dns_get_mx($domain, $mxHosts, $weight);
		$mxHosts = self::weightedMX($mxHosts, $weight);
		if ( $mxHosts ) {
			$n = 0;
			$lastMX = '';
			foreach($mxHosts as $mxHost) {
				$lastMX = $mxHost;
				$n++;
				if($sock = @fsockopen($mxHost, 25, $errno , $errstr, $timeout)) {
					$response = fgets($sock);

					$responseInfo = explode(" ", $response);
					$responseCode = intval( reset($responseInfo) );
					if ( in_array($responseCode, array(420, 421)) ) {

						//todo
						CheckMailbox::logMailBoxRequest($email, $returnStatuses['ConnectionClosed'], $lastMX);

						return 0;
						return $returnStatuses['ConnectionClosed'];
					}

					stream_set_timeout($sock, $timeout);
					$meta = stream_get_meta_data($sock);

					$cmds = array(
						"HELO $helo_domain",
						"MAIL FROM: <$from_address>",
						"RCPT TO: <$email>",
						"QUIT",
					);


					# Hard error on connect -> break out
					# Error means 'any reply that does not start with 2xx '
					if(!$meta['timed_out'] && !preg_match('/^2\d\d[ -]/', $response)) {
						break;
					}
					foreach($cmds as $cmd) {
						fputs($sock, "$cmd\r\n");
						$response = fgets($sock, 4096);

						if(preg_match('/^550 5.5.1/', $response)) {
							CheckMailbox::logMailBoxRequest($email, $returnStatuses['NoMailbox'], $lastMX);
							return 0;
						}
						if(!$meta['timed_out'] && preg_match('/^5\d\d[ -]/', $response)) {
							if ( $numberOfRetry >= 0 ) {
								sleep(3);
								return self::checkEmailBySMTP($email, $helo_domain, $from_address, $timeout, ($numberOfRetry-1));
							}
							CheckMailbox::logMailBoxRequest($email, $returnStatuses['NoMailbox'], $lastMX);

							return $returnStatuses['NoMailbox'];
						}
					}
					fclose($sock);

					CheckMailbox::logMailBoxRequest($email, $returnStatuses['OK'], $lastMX);

					return $returnStatuses['OK'];
				}
			}

			CheckMailbox::logMailBoxRequest($email, $returnStatuses['ConnectionTimeout'], $lastMX);

			return 0;
			return $returnStatuses['ConnectionTimeout'];
		}

		CheckMailbox::logMailBoxRequest($email, $returnStatuses['DNS'], $lastMX);

		return $returnStatuses['DNS'];
	}


	/**
	 * @param $mxhosts
	 * @param $weight
	 * @return array
	 */
	private static function weightedMX($mxhosts, $weight) {

		$result = array();
		foreach($mxhosts as $k=>$host) {

			$result [$weight[$k]] = $host;
		}

		ksort($result);
		return $result;
	}
} 