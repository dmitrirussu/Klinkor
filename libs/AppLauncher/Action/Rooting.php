<?php
/**
 * Created by Dumitru Russu.
 * Date: 07.11.2013
 * Time: 17:22
 * To change this template use File | Settings | File Templates.
 */

namespace AppLauncher\Action;

use AppLauncher\Action\Exceptions\RootingException;
use AppLauncher\RegisterApp;

class Rooting {

	const LANG_CODE = 'en';
	const FILE_NAME = 'actions.ini';

	private static $CONF_PATHS = array();

	private static $ALL_URLS = array();
	private static $PARSED_URLS = array();
	private static $INSTANCE = null;
	private static $LANG_CODE = self::LANG_CODE;
	private static $APP_NAME = null;

	private function __construct() {}
	private function __clone(){}

	public static function instance() {

		if ( empty(self::$INSTANCE) ) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	/**
	 * Set Path
	 * @param $path
	 * @return $this
	 */
	public function setPath($path) {

		self::$CONF_PATHS[] = $path;

		return $this;
	}

	/**
	 * Set Global App Name
	 * @param $appName
	 * @return $this
	 */
	public function setAppName($appName) {

		self::$APP_NAME = $appName;

		return $this;
	}

	public static function getAppName() {

		return self::$APP_NAME;
	}

	/**
	 * Set App Lang
	 * @param string $langCode
	 * @return $this
	 */
	public function setLangCode($langCode = self::LANG_CODE) {

		self::$LANG_CODE = $langCode;

		return $this;
	}

	/**
	 * @param $classAndAction
	 * @throws RootingException
	 * @return array|bool|mixed
	 */
	public static function url($classAndAction) {

		$langCode = self::$LANG_CODE . ':';
		$key = $langCode . $classAndAction;
		$separator = '/';
		if ( defined('APP_FOLDER') ) {
			if ( APP_FOLDER ) {
				$separator = '/'.APP_FOLDER.'/';
			}
		}

		if ( isset(self::$PARSED_URLS[$key]) ) {
			return $separator.trim(self::$PARSED_URLS[$key], '/');
		}


		if ( strpos($classAndAction, ':') === false) {

			if ( strpos($classAndAction, '->') !== false ) {

				$actionData = explode('->', $classAndAction);
				$action = null;

				if ( strpos($classAndAction, 'defaultAction') === false) {

					$action = '/'.str_replace('Action', '', $actionData[1]);
				}

				return $separator.str_replace('Controller', '', $actionData[0]). $action;
			}

			return $classAndAction;
		}

		$classActions = explode('->', $classAndAction);

		if ( !$classActions ) {

			return $classAndAction;
		}

		$data = self::getRootesIni();

		list($className, $actionName) = $classActions;

		if ( $data ) {
			foreach($data AS $actions ) {

				foreach($actions as $url => $urlDetails) {
					$details = explode('->', $urlDetails['action']);
					$urls = explode(':', $url);

					if (isset($urls[0]) && strcasecmp($urls[0], self::$LANG_CODE) === 0 &&
						in_array($className, $details) && in_array($actionName, $details) ) {

						return $separator.self::$PARSED_URLS[$key] = trim($urls[1], '/');
					}
				}
			}

		}

		$action = substr($actionName, 0, strlen($actionName) - strlen('Action'));
		$action = ($action === 'default' ? '' : '/'.$action);
		$app = explode('::', $className);

		if ( !isset($app[0]) ) {

			throw new RootingException('Missing Url = ' .$classAndAction);
		}

		$appName = '/'.(strpos(self::getAppName(), $app[0]) !== false ? '' : $app[0]);
		$className = '/'.(isset($app[1]) ? str_replace('Controller', '', $app[1]) : 'Default');

		return $separator.trim($appName.$className.$action, '/');
	}

	/**
	 * @param $classAndAction
	 * @throws RootingException
	 * @return array|bool|mixed
	 */
	public static function urlRelative($classAndAction) {
		$langCode = self::$LANG_CODE . ':';
		$key = $langCode . $classAndAction;

		if ( isset(self::$PARSED_URLS[$key]) ) {
			return trim(self::$PARSED_URLS[$key], '/');
		}


		if ( strpos($classAndAction, ':') === false) {
			if ( strpos($classAndAction, '->') !== false ) {
				$actionData = explode('->', $classAndAction);
				$action = null;

				if ( strpos($classAndAction, 'defaultAction') === false) {

					$action = '/'.str_replace('Action', '', $actionData[1]);
				}

				return str_replace('Controller', '', $actionData[0]). $action;
			}

			return $classAndAction;
		}

		$classActions = explode('->', $classAndAction);

		if ( !$classActions ) {

			return $classAndAction;
		}

		$data = self::getRootesIni();

		list($className, $actionName) = $classActions;

		if ( $data ) {
			foreach($data AS $actions ) {

				foreach($actions as $url => $urlDetails) {
					$details = explode('->', $urlDetails['action']);
					$urls = explode(':', $url);

					if (isset($urls[0]) && strcasecmp($urls[0], self::$LANG_CODE) === 0 &&
						in_array($className, $details) && in_array($actionName, $details) ) {

						return self::$PARSED_URLS[$key] = trim($urls[1], '/');
					}
				}
			}

		}

		$action = substr($actionName, 0, strlen($actionName) - strlen('Action'));
		$action = ($action === 'default' ? '' : '/'.$action);
		$app = explode('::', $className);

		if ( !isset($app[0]) ) {

			throw new RootingException('Missing Url = ' .$classAndAction);
		}

		$appName = '/'.(strpos(self::getAppName(), $app[0]) !== false ? '' : $app[0]);
		$className = '/'.(isset($app[1]) ? str_replace('Controller', '', $app[1]) : 'Default');

		return trim($appName.$className.$action, '/');
	}

	/**
	 * Get Class And Action Info By Url
	 *
	 * @param $url
	 * @throws RootingException
	 * @return array
	 */
	public static function getInfoByUrl($url) {

		$data = self::getRootesByUrl($url, self::$LANG_CODE);

		if( !$data ) {

			$result = self::getInfoFromParsedUrl($url);

			if ( $result ) {

				return $result;
			}

			if ( !class_exists($url) ) {

				throw new RootingException('This Controller does not CN-NAME: ' . $url);
			}

			return array(
				'class' => $url,
				'action' => 'defaultAction'
			);
		}

		if ( !isset($data[0]) ) {
			throw new RootingException('Not found controller url: ' . $url);
		}

		$appName = explode('::', $data[0]);
		$controllerName = $appName[1];
		$appName = explode('\\', self::getAppName());
		$appName = $appName[0].'\\Controllers\\'.$controllerName;
		$appControllerName = $appName;

		return array(
			'class' => $appControllerName,
			'action' => $data[1]
		);
	}

	/**
	 * Get Info from Parsed URL
	 * @param $url
	 * @return array
	 * @throws RootingException
	 */
	private static function getInfoFromParsedUrl($url) {
		$url = rtrim($url, '/');
		$urlInfo = explode('/', $url);


		if ( count($urlInfo) > 0) {
			$className = (isset($urlInfo[1]) ? ucfirst($urlInfo[1]) : 'Default').'Controller';
			$actionName = (isset($urlInfo[2]) ? $urlInfo[2] : 'default').'Action';

			$appObjects = RegisterApp::instance()->getRegisteredApps();
			array_push($appObjects, RegisterApp::instance()->getBaseApp());


			$appName = (strpos(ucfirst($urlInfo[0]), 'App') !== false ? ucfirst($urlInfo[0]) : ucfirst($urlInfo[0]).'App');
			$appClassName = $appName.'\\'.$appName;


			if ( class_exists($appClassName) ) {
				$appClassObject = new $appClassName();

				//return this controller if is registered
				if ( !in_array($appClassObject, $appObjects) ) {
					$appName = explode('\\', self::getAppName());
					$appName = $appName[0];
				}
				$controllerName = $appName.'\Controllers\\'.$className;
			}
			elseif(class_exists('AppLauncher\\'.$appClassName)) {
				$appClassName = 'AppLauncher\\'.$appClassName;
				$appClassObject = new $appClassName();

				//return this controller if is registered
				if ( !in_array($appClassObject, RegisterApp::instance()->getRegisteredApps()) ) {

					$appName = explode('\\', self::getAppName());
					$appName = $appName[0];
				}

				$controllerName = 'AppLauncher\\'.$appName.'\Controllers\\'.$className;
			}
			elseif (count($urlInfo) > 1) {
				$className = (isset($urlInfo[0]) ? ucfirst($urlInfo[0]) : 'Default').'Controller';
				$actionName = (isset($urlInfo[1]) ? $urlInfo[1] : 'default').'Action';

				$appName = explode('\\', self::getAppName());
				$appName = $appName[0];

				$controllerName = $appName.'\Controllers\\'.$className;
			}
			else {
				$className = (isset($urlInfo[0]) ? ucfirst($urlInfo[0]) : 'Default').'Controller';
				$appName = explode('\\', self::getAppName());
				$appName = $appName[0];
				$controllerName = $appName.'\Controllers\\'.$className;
			}

			if ( !class_exists($controllerName) ) {
				throw new RootingException('This Controller does not exist CN-NAME: ' . $controllerName);
			}

			return array(
				'class' => $controllerName,
				'action' => $actionName
			);
		}

		return array();
	}

	/**
	 * Get Rootes
	 * @param $url
	 * @return array
	 */
	private static function getRootesByUrl($url) {
		$actionData = array();
		$data = self::getRootesIni();

		$key = self::$LANG_CODE.':'.$url;

		if ( $data ) {

			foreach($data AS $actions) {

				if ( isset($actions[$key]) && isset($actions[$key]['action']) ) {

					$actionData = explode('->', $actions[$key]['action']);
					break;
				}
			}
		}

		return $actionData;
	}

	/**
	 * Get Rootes Ini
	 * @throws RootingException
	 * @return array
	 */
	private static function getRootesIni() {

		if ( self::$CONF_PATHS ) {
			foreach(self::$CONF_PATHS AS $path) {
				$fullPath = $path.self::FILE_NAME;

				if ( file_exists($fullPath) ) {

					self::$ALL_URLS[] = parse_ini_file($fullPath, true);
				}
				else {
					throw new RootingException('Not found rooting actions file, in directory = ' . $fullPath);
				}
			}
		}

		return self::$ALL_URLS;
	}
}
