<?php
/**
 * Created by Dumitru Russu.
 * Date: 17.05.2014
 * Time: 16:17
 * ${NAMESPACE}${NAME}
 */

namespace AppLauncher;

use AppLauncher\Exceptions\RegisterAppFacadeException;
use AppLauncher\Interfaces\AppControllerInterface;
use AppLauncher\Action\Exceptions\RoutingException;
use AppLauncher\Action\Request;
use AppLauncher\Action\Response;
use AppLauncher\Action\Routing;
use AppLauncher\Utils\MethodUtils;

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

	public function __construct(Request $request, Routing $rooting) {
		$this->request = $request;
		$this->rooting = $rooting;
		try {
			$page = $this->request->get('page', 'char', 'Default');
			$page = (empty($page) ? 'Default' : $page);

			$this->requestInfo = $this->rooting->getInfoByUrl($page);

			$this->controller = $this->requestInfo['class'];
			$this->action = $this->requestInfo['action'];
		}
		catch(RoutingException $e) {
			if (Launch::isDevEnvironment()) {
				Request::session()->setVar('exception', $e);
				Request::redirect(Routing::url('ErrorApp::DefaultController->defaultAction'));
			}
			else {
				Request::redirect(Routing::url('ErrorApp::Page404Controller->defaultAction'));
			}
		}
		catch(\Exception $e) {

			if ( Launch::isDevEnvironment() ) {
				Request::session()->setVar('exception', $e);
				Request::redirect(Routing::url('ErrorApp::DefaultController->defaultAction'));
			}
			else {
				Request::redirect(Routing::url('ErrorApp::Page404Controller->defaultAction'));
			}
		}
	}

	public function registerMainApps(\ReflectionClass $reflectionControllerClass) {

		if ( strpos($mainAppName = str_replace('\Controllers', '', $reflectionControllerClass->getNamespaceName()), 'AppLauncher') === false ) {
			HTML::instance()->setMainAppName($mainAppName);
			$this->registerMainApps($reflectionControllerClass->getParentClass());
		}
	}

	public function display() {
		try {

			if ( $this->controller ) {
				$reflectionControllerClass = new \ReflectionClass($this->controller);

				$controllerProjectName = $reflectionControllerClass->getParentClass()->getNamespaceName();
				$tplDirectory = explode('\\', strtolower(str_replace('Controller', '', $this->controller)));
				$this->tplDirectory = strtolower(end($tplDirectory));


				//check if class has method
				if ( !$reflectionControllerClass->hasMethod($this->action) ) {

					throw new RegisterAppFacadeException('Controller ['. $this->controller
						.'], Action method ['. $this->action
						.'] does not exist');
				}

				//setup rooting Languages
				Routing::instance()->setLangCode(Request::session()->getVar('lang', Request::get('lang', 'char', \MyAccountApp\MyAccountApp::DEFAULT_LANG_CODE)));

				//Run Controller
				$this->controller = new $this->controller(Routing::instance()->getLangCode());

				if ( $this->controller->hasForceHTTPSRequest() ) {
					$this->controller->doRedirectToHTTPS();
				}
				elseif($this->controller->hasForceHTTPRequest()) {
					$this->controller->doRedirectToHTTP();
				}


				if ( !$this->controller->isLogged() && $this->controller->isSecured() ) {
					if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
						http_response_code(401);
						exit( 'Not authorized' );
					}
					Request::redirect(Routing::url('DefaultController->defaultAction'));
				}


				//Set Is Project App
				$currentRunningApp = str_replace('\Controllers', '', $reflectionControllerClass->getNamespaceName());
				RegisterApp::instance()->addRunningApp("{$currentRunningApp}\\{$currentRunningApp}");
				Routing::instance()->setCurrentRunningApp($currentRunningApp);
				HTML::instance()
					->setRegisterApp(RegisterApp::instance())
					->setActionViewDirectory($this->tplDirectory);


				//Bind Args to method
				$reflectionMethod = new \ReflectionMethod($this->controller, $this->action);
				$requestArgs = [];
				if ($methodArgs = MethodUtils::readControllerMethodTokensFromDocComment($reflectionMethod->getDocComment()) ) {
					foreach ($methodArgs AS $methodArg) {
						if ( $methodArg['type'] === 'file' ) {
							$requestArgs[$methodArg['name']] = $this->controller->getRequest()->getAllFileData($methodArg['name']);
						}
						else {
							$requestArgs[$methodArg['name']] = $this->controller->getRequest()->request($methodArg['name'], $methodArg['type'], $methodArg['default_value'], ($methodArg['pattern'] === 'true' || empty($methodArg['pattern']) ? true : false));
						}
					}
				}

				$this->response = call_user_func_array(array($this->controller, $this->action), $requestArgs);

				if( empty($this->response)) {
					throw new RegisterAppFacadeException('Missing Action Response AC-NAME: ' . $this->action);
				}

				if ( is_array($this->response) ) {
					$this->response = new Response($this->response);
				}




				$this->controller->assign('errors', $this->controller->getErrorMessages());
				$this->controller->assign('successMessages', $this->controller->getSuccessMessages());

				switch($this->response->getType()) {
					case 'h':
					case 'html': {

						if ( $this->response->getErrorCode() !== 200) {
							MethodUtils::http_response_code($this->response->getErrorCode());
							exit($this->response->getErrorMessage());
						}

						//unset errors
						$this->controller->getRequest()->session()->unsetVar('errors');
						$this->controller->getRequest()->session()->unsetVar('successMessage');

						$this->displayLayoutTemplates();

						break;
					}
					case 'hb':
					case 'html_block': {
						if ( $this->response->getErrorCode() !== 200) {
							MethodUtils::http_response_code($this->response->getErrorCode());
							exit($this->response->getErrorMessage());
						}

						$this->displayHTMLBlock();
						break;
					}
					case 't':
					case 'text': {
						if ( $this->response->getErrorCode() !== 200) {
							MethodUtils::http_response_code($this->response->getErrorCode());
							exit($this->response->getErrorMessage());
						}

						$this->displayText();
						break;
					}
					case 'x':
					case 'xml': {
						if ( $this->response->getErrorCode() !== 200) {
							MethodUtils::http_response_code($this->response->getErrorCode());
							exit($this->response->getErrorMessage());
						}
						$this->displayXml();
						break;
					}
					case 'j':
					case 'json': {
						if ( $this->response->getErrorCode() !== 200) {
							MethodUtils::http_response_code($this->response->getErrorCode());
							exit($this->response->getErrorMessage());
						}

						$this->displayJsonEncodedString();

						break;
					}
					case 'f':
					case 'file': {
						if ( $this->response->getErrorCode() !== 200) {
							MethodUtils::http_response_code($this->response->getErrorCode());
							exit($this->response->getErrorMessage());
						}
						$this->displayFile();
						break;
					}
					case 'd':
					case 'download' : {
						if ( $this->response->getErrorCode() !== 200) {
							MethodUtils::http_response_code($this->response->getErrorCode());
							exit($this->response->getErrorMessage());
						}
						$this->forceDownloadFile($controllerProjectName);

						break;
					}
					case 'r':
					case 'redirect': {

						$url = $this->response->getDisplay() || $this->response->getUrl();

						if ( !filter_var($this->response->getDisplay(), FILTER_VALIDATE_URL) || !filter_var($this->response->getUrl(), FILTER_VALIDATE_URL) ) {

							if ( $this->response->getDisplay() ) {
								$url = Routing::url($this->response->getDisplay());
							}
							else {

								$url = Routing::url($this->response->getUrl());
							}
						}

						Request::redirect($url, ($this->response->getIsHttps() || $this->controller->hasForceHTTPSRequest()), $this->response->getIsLocale());

						break;
					}
					default : {
						if ( $this->response->getErrorCode() !== 200) {
							MethodUtils::http_response_code($this->response->getErrorCode());
							exit($this->response->getErrorMessage());
						}
						$this->displayHTMLBlock();
						break;
					}
				}
			}
		}
		catch(\Exception $e) {
			if ( Launch::isDevEnvironment() ) {

				Request::session()->setVar('exception', $e);
				Request::redirect(Routing::url('ErrorApp::DefaultController->defaultAction'));
			}
			else {

				Request::redirect(Routing::url('ErrorApp::Page404Controller->defaultAction'));
			}
		}
	}

	private function forceDownloadFile($controllerProjectName) {

		$filePath = $this->response->getFileName();
		if ( !is_file($this->response->getFileName()) ) {
			$filePath = PATH_PUBLIC . $controllerProjectName . DIRECTORY_SEPARATOR . $this->response->getFileName();
		}

		$fileName = ($this->response->getNewFileName() ? $this->response->getNewFileName() : basename($filePath));

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"{$fileName}\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filePath));
		@readfile($filePath);
		exit;
	}

	/**
	 * Display Layout Template
	 * @param $controllerProjectName
	 */
	private function displayLayoutTemplates() {
		$this->controller->assign('cssFiles', Scripts::instance()->getCssFiles());
		$this->controller->assign('javaScriptFiles', Scripts::instance()->getScriptsJs());
		$this->controller->assign('tplName', $this->response->getDisplay());
		$this->controller->assign('breadCrumbs', $this->controller->getBreadCrumbs());


		$this->controller->assign('globalVars', $this->controller->getAssignedVars());

		if ( $this->controller->getAssignedVars() ) {

			foreach($this->controller->getAssignedVars() AS $key => $value) {
				$$key = $value;
			}
		}

		ob_start();

		foreach(RegisterApp::instance()->getCurrentRunningAppPrentApps() AS $appInfo) {

			$includeBaseTPL = PATH_APP . $appInfo['app'] . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR. 'index' . HTML::TPL_EXT;

			if ( is_file($includeBaseTPL) ) {
				require_once $includeBaseTPL;
				break;
			}

		}

		ob_end_flush();
	}

	private function displayText() {
		return print ($this->response->getDisplay());
	}

	private function displayXml() {
		header("Content-type: text/xml; charset=utf-8");
		return print ($this->response->getDisplay() ? $this->response->getDisplay() : $this->response->getFileName());
	}

	/**
	 * Display HTML Block
	 */
	private function displayHTMLBlock() {

		HTML::block($this->response->getDisplay(),
			$this->controller->getAssignedVars());
	}

	/**
	 * Display Json encode String
	 */
	private function displayJsonEncodedString() {
		$data = $this->response->getData();

		if ( $this->controller->getRequest()->isAjax() || $this->controller->getRequest()->isApiRequest()) {
			header('Content-Type: application/json');
			ob_start();
			print(json_encode($data));
			ob_end_flush();
			exit;
		}
		else {
			//On missing ajax request do redirect instead to answer
			if ( isset($data['error']) && $data['error']) {
				$this->controller->addErrorMessage($data['message']);
			}
			else {
				$this->controller->addSuccessMessage($data['message']);
			}

			if ( isset($_SERVER['HTTP_REFERER']) ) {
				header('Location: '. $_SERVER['HTTP_REFERER']);
				exit;
			}
			else {
				Request::redirect(
					Routing::url(str_replace(array('App\\Controllers\\',), array('::'), $this->requestInfo['class'])."->defaultAction"),
					($this->response->getIsHttps() || $this->controller->hasForceHTTPSRequest()),
					$this->response->getIsLocale()
				);
			}
		}

		MethodUtils::http_response_code(204);
		exit;
	}

	/**
	 * Display File
	 */
	private function displayFile() {

		ob_start();
		// Define HTTP header fields.
		header('Content-Type: ' . $this->response->getContentType());
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-Length: '.filesize($this->response->getFileName()));
		header('Content-Disposition: inline; filename='.basename($this->response->getFileName()));
		header('Content-Transfer-Encoding: binary');

		print (file_get_contents($this->response->getFileName()));
		ob_end_flush();
		exit;
	}
}

