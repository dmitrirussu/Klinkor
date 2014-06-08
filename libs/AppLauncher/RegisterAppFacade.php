<?php
/**
 * Created by Dumitru Russu.
 * Date: 17.05.2014
 * Time: 16:17
 * ${NAMESPACE}${NAME} 
 */

namespace AppLauncher;

use AppLauncher\Interfaces\AppControllerInterface;
use AppLauncher\Action\Exceptions\RootingException;
use AppLauncher\Action\Request;
use AppLauncher\Action\Response;
use AppLauncher\Action\Rooting;

class RegisterAppFacade {

	private $request;
	private $rooting;

	private $requestInfo;
	/**
	 * @var AppControllerInterface
	 */
	private $controller;
	private $action;

	/**
	 * @var Response
	 */
	private $response;
	private $tplDirectory;

	public function __construct(Request $request, Rooting $rooting) {
		$this->request = $request;
		$this->rooting = $rooting;
		try {

			$this->requestInfo = $this->rooting->getInfoByUrl($this->request->get('page', 'char', 'default'));

			$this->controller = $this->requestInfo['class'];
			$this->action = $this->requestInfo['action'];
		}
		catch(RootingException $e) {
			echo($e->getMessage());
			print_r($e->getTrace());

		}
		catch(\Exception $e) {
			echo($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function display() {

		if ( $this->controller ) {
			$reflectionControllerClass = new \ReflectionClass($this->controller);
			$controllerProjectName = str_replace('\\Controllers', '', $reflectionControllerClass->getNamespaceName());
			$isProjectApp = (!in_array($controllerProjectName, HTML::instance()->getApps()) ? true : false);

			//Set Is Project App
			HTML::instance()
				->setIsProjectApp($isProjectApp)
				->setAppName($controllerProjectName);

			$tplDirectory = explode('\\', strtolower(str_replace('Controller', '', $this->controller)));
			$this->tplDirectory = end($tplDirectory);

			$this->controller = new $this->controller();

			/**
			 * @var $result Response
			 */
			$this->response = $this->controller->{$this->action}();

			if ( is_array($this->response) ) {

				$this->response = new Response($this->response);
			}

			switch($this->response->getType()) {
				case 'h':
				case 'html': {

					$this->displayLayoutTemplates($isProjectApp, $controllerProjectName);
					break;
				}
				case 'hb':
				case 'html_block': {

					$this->displayHTMLBlock();
					break;
				}
				case 'j':
				case 'json': {

					$this->displayJsonEncodedString();
					break;
				}
				case 'r':
				case 'redirect': {

					$url = Rooting::url($this->response->getDisplay());
					header('Location: ' . $url);

					break;
				}
				default : {

					$this->displayHTMLBlock();
					break;
				}
			}
		}
	}

	/**
	 * Display Layout Template
	 * @param $isProjectApp
	 * @param $controllerProjectName
	 */
	private function displayLayoutTemplates($isProjectApp, $controllerProjectName) {

		$this->controller->assign('javaScriptFiles', $this->controller->getAssignedJavaScriptFiles());
		$this->controller->assign('cssFiles', $this->controller->getAssignedCSSFiles());
		$this->controller->assign('tplName', $this->tplDirectory . DIRECTORY_SEPARATOR . $this->response->getDisplay());

		if ( $this->controller->getAssignedVars() ) {

			foreach($this->controller->getAssignedVars() AS $key => $value) {

				$$key = $value;
			}
		}

		ob_start();

		if ( $isProjectApp ) {
			$includeBaseTPL = PATH_APP . $controllerProjectName . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR. 'index' . HTML::TPL_EXT;

			if ( file_exists($includeBaseTPL) ) {

				require_once $includeBaseTPL;
			}
		}
		else {

			foreach(HTML::instance()->getApps() AS $appName) {

				$includeBaseTPL = PATH_APP . $appName . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR. 'index' . HTML::TPL_EXT;

				if ( file_exists($includeBaseTPL) ) {

					require_once $includeBaseTPL;
					break;
				}
			}
		}

		ob_end_flush();
	}

	/**
	 * Display HTML Block
	 */
	private function displayHTMLBlock() {

		HTML::block($this->tplDirectory . DIRECTORY_SEPARATOR . $this->response->getDisplay(),
			$this->controller->getAssignedVars());
	}

	/**
	 * Display Json encode String
	 */
	private function displayJsonEncodedString() {

		$jsonString = $this->response->getDisplay();

		ob_start();
		header('Content-Type: application/json');
		if ( is_array($this->response->getDisplay()) ) {

			$jsonString = json_encode($this->response->getDisplay());
		}

		echo($jsonString);

		ob_end_flush();
	}
}

