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


	/**
	 * @return mixed
	 * @throws \AppLauncher\Exceptions\ResponseException
	 */
	public function getDisplay() {

		if ( !isset($this->responseData['display']) && $this->getType() === 'html' && $this->getType() === 'html_block' ) {
			throw new ResponseException('Action Response attribute [display] cannot be empty, ' .
				'define response display=>(file name, rooting url)');

		}

		return (isset($this->responseData['display']) ? $this->responseData['display'] : null);
	}


	/**
	 * @return mixed
	 * @throws \AppLauncher\Exceptions\ResponseException
	 */
	public function getFileName() {

		if ( !isset($this->responseData['filename']) ) {

			throw new ResponseException('Action Response attribute [filename] cannot be empty, ' .
				'define response filename=>(file_name.pdf or image.jpg or file_name.doc)');
		}

		return $this->responseData['filename'];
	}


	/**
	 * @return mixed
	 * @throws \AppLauncher\Exceptions\ResponseException
	 */
	public function getType() {

		if( !isset($this->responseData['type']) ) {

			throw new ResponseException('Action Response attribute [type] cannot be empty, ' .
				'define response type=>(html, json, redirect)');
		}

		return $this->responseData['type'];
	}


	/**
	 * @return mixed
	 * @throws \AppLauncher\Exceptions\ResponseException
	 */
	public function getContentType() {

		if( !isset($this->responseData['content_type']) ) {

			throw new ResponseException('Action Response attribute [content_type] cannot be empty, ');
		}

		return $this->responseData['content_type'];
	}


	/**
	 * @return mixed
	 * @throws \AppLauncher\Exceptions\ResponseException
	 */
	public function getUrl() {

		if( !isset($this->responseData['url']) ) {

			throw new ResponseException('Action Response attribute [url] cannot be empty, ');
		}

		return $this->responseData['url'];
	}


	/**
	 * @return array
	 */
	public function getResponseData() {

		return $this->responseData;
	}
}
