<?php

namespace AppLauncher\Utils;


class SoapClient extends \SoapClient {


	public function __doRequest($request, $location, $action, $version = SOAP_1_2, $one_way = 0){

		$xml = explode("\r\n", parent::__doRequest($request, $location, $action, $version, $one_way));
//		$response = preg_replace( '/^(\x00\x00\xFE\xFF|\xFF\xFE\x00\x00|\xFE\xFF|\xFF\xFE|\xEF\xBB\xBF)/', "", $xml[5] );
		return $xml;
	}
}
