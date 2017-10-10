<?php
/**
 * Created by Dumitru Russu.
 * Date: 27.01.2016
 * Time: 11:22
 * AppLauncher\Utils${NAME} 
 */

namespace AppLauncher\Utils;


class CURL {

	private $ini;
	private $url;
	private $data;
	private $dataResult;

	public static $USE_SSL = true;
	private static $USE_SSL_CERT = false;
	private static $SSL_CERT_PATH = '/var/www/popcompta/tests';
	private static $SSL_CERT_NAME = 'crm-pk.pem';
	private static $SSL_CERT_PASSWORD = 'fajke!23';


	public function __construct($url, $data = array()) {
		$this->ini = curl_init();
		$this->url = $url;
		$this->data = $data;
	}

	public function setCertificateFile($file) {
		if ( !is_file($file) ) {
			throw new \Exception('Missing certificate file');
		}
		self::$USE_SSL_CERT = true;

		$fileInfo = pathinfo($file);
		self::$SSL_CERT_PATH = $fileInfo['dirname'];
		self::$SSL_CERT_NAME = $fileInfo['filename'];
	}

	public function setCertificationPassword($password) {
		self::$SSL_CERT_PASSWORD = $password;
	}

	/**
	 * Get Parse Data
	 * @return bool|string
	 */
	private function getParseData() {

		if ( !$this->data ) {
			return false;
		}
		$dataResult = array();

		foreach ($this->data as $key => $value ) {
			$dataResult[] = $key.'='.$value;
		}

		return join('&', $dataResult);
	}

	/**
	 * Do Request
	 */
	public function doRequest($followLocation = true, $returnTransferData = true) {

		if ( self::$USE_SSL ) {
			curl_setopt($this->ini, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($this->ini, CURLOPT_SSL_VERIFYHOST, 0);

			if ( self::$USE_SSL_CERT ) {
				curl_setopt($this->ini,CURLOPT_SSLKEYTYPE, 'PEM');
				curl_setopt($this->ini, CURLOPT_CAPATH, self::$SSL_CERT_PATH);
				curl_setopt($this->ini, CURLOPT_SSLCERT, self::$SSL_CERT_PATH.self::$SSL_CERT_NAME);
				curl_setopt($this->ini, CURLOPT_SSLKEY, self::$SSL_CERT_PATH.self::$SSL_CERT_NAME);
				curl_setopt($this->ini, CURLOPT_CAINFO, self::$SSL_CERT_PATH.self::$SSL_CERT_NAME);
				curl_setopt($this->ini, CURLOPT_SSLCERTPASSWD, self::$SSL_CERT_PASSWORD);
				curl_setopt($this->ini, CURLOPT_VERBOSE, 1);
			}
		}

		curl_setopt($this->ini, CURLOPT_FOLLOWLOCATION, $followLocation);
		curl_setopt($this->ini, CURLOPT_HEADER, false);
		curl_setopt($this->ini, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($this->ini, CURLOPT_TIMEOUT, 10);
		curl_setopt($this->ini, CURLOPT_URL, $this->url);
		curl_setopt($this->ini, CURLOPT_COOKIESESSION, false);
		curl_setopt($this->ini, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; .NET CLR 1.1.4322; .NET CLR 1.0.3705)');
		curl_setopt($this->ini, CURLOPT_RETURNTRANSFER, $returnTransferData);

		if ( $this->getParseData() ) {

			curl_setopt($this->ini, CURLOPT_POST, count($this->data));
			curl_setopt($this->ini, CURLOPT_POSTFIELDS, $this->data);
		}

		$this->dataResult = curl_exec($this->ini);
	}

	/**
	 * Get Data
	 * @return bool|string
	 */
	public function getData() {

		if ( $this->getErrors() ) {
			return false;
		}

		return $this->dataResult;
	}

	/**
	 * Get Errors
	 * @return string
	 */
	public function getErrors() {
		return curl_error( $this->ini );
	}

	public function __destruct() {
		unset($this->data);
		curl_close($this->ini);
	}


}