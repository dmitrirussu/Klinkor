<?php
/**
 * Created by Dumitru Russu.
 * Date: 15.04.2014
 * Time: 20:54
 * ${NAMESPACE}${NAME} 
 */

namespace AppLauncher;


use ErrorApp\ErrorAppController;
use AppLauncher\Interfaces\AppControllerInterface;
use AppLauncher\Interfaces\RegisterAppInterface;

class Launch {

	const ENV_DEV = 'dev';
	const ENV_PROD = 'prod';

	private static $CURRENT_ENV = self::ENV_PROD;

	private function __construct() {}
	private function __clone() {}

	/**
	 * App launcher
	 * @param AppControllerInterface $baseApp
	 * @param string $env
	 * @return RegisterAppInterface
	 */
	public static function app(AppControllerInterface $baseApp, $env = self::ENV_PROD) {
		self::$CURRENT_ENV = $env;
		self::displayErrors();

		//App Registrar
		return RegisterApp::instance()->addBaseApp($baseApp)->addApp(new ErrorAppController());
	}

	/**
	 * Check is Dev Environment
	 * @return bool
	 */
	public static function isDevEnvironment() {

		return self::ENV_DEV === self::$CURRENT_ENV;
	}

	/**
	 * Check is Dev Environment
	 * @return bool
	 */
	public static function isProdEnvironment() {

		return self::ENV_PROD === self::$CURRENT_ENV;
	}

	/**
	 * Display Errors
	 */
	private static function displayErrors() {
		if ( self::isDevEnvironment() ) {
			ini_set('display_startup_errors', 1);
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		}
	}
}
