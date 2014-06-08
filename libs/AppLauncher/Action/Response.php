<?php
/**
 * Created by Dumitru Russu.
 * Date: 04.05.2014
 * Time: 16:56
 * AppLauncher${NAME} 
 */

namespace AppLauncher\Action;


use AppLauncher\Exceptions\ResponseException;

class Response {

	private static $_DISPLAY = array('display' => 'index', 'type' => 'html', 'layout' => 'index');
	private static $_REDIRECT = array('display' => 'DemoApp::DefaultController->defaultAction', 'type' => 'redirect');

	private $responseData;

	public function __construct(array $response = array('display' => 'index', 'type' => 'html')) {

		$this->responseData = $response;
	}

	public function getDisplay() {

		if ( !isset($this->responseData['display']) ) {

			throw new ResponseException('Action Response attribute [type] cannot be empty, ' .
				'define response type=>(html, json, redirect)');
		}

		return $this->responseData['display'];
	}

	public function getType() {

		if( !isset($this->responseData['display']) ) {

			throw new ResponseException('Action Response attribute [display] cannot be empty, ' .
				'define response display=>(file name, rooting url)');
		}

		return $this->responseData['type'];
	}

	public function getResponseData() {

		return $this->responseData;
	}
}
