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
			$tplDirectory = end($tplDirectory);

			$this->controller = new $this->controller();

			/**
			 * @var $result Response
			 */
			$result = $this->controller->{$this->action}();

			if ( is_array($result) ) {

				$result = new Response($result);
			}

			$this->controller->assign('tplName', $tplDirectory . DIRECTORY_SEPARATOR . $result->getTemplate());

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
	}

}

