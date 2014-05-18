<?php
/**
 * Created by Dumitru Russu.
 * Date: 04.05.2014
 * Time: 16:56
 * AppLauncher${NAME} 
 */

namespace AppLauncher\Action;


class Response {

	private static $_DISPLAY = array('display' => 'index', 'type' => 'html');
	private static $_REDIRECT = array('redirect' => 'DemoApp::DefaultController->defaultAction');

	private $responseData;

	public function __construct(array $response = array('display' => 'index', 'type' => 'html')) {

		$this->responseData = $response;
	}

	public function getUrl() {

		return $this->responseData['redirect'];
	}

	public function getTemplate() {

		return $this->responseData['display'];
	}

	public function getType() {

		return $this->responseData['type'];
	}

	public function getResponseData() {

		return $this->responseData;
	}
} 