<?php
/**
 * Created by Dumitru Russu.
 * Date: 15.04.2014
 * Time: 20:54
 * ${NAMESPACE}${NAME} 
 */

namespace AppLauncher;


use AppLauncher\Interfaces\AppControllerInterface;
use AppLauncher\Interfaces\RegisterAppInterface;

class Launch {

	const ENV_DEV = 'dev';
	const ENV_PROD = 'prod';


	private function __construct() {}
	private function __clone() {}

	/**
	 * App launcher
	 * @param AppControllerInterface $baseApp
	 * @param string $env
	 * @return RegisterAppInterface
	 */
	public static function app(AppControllerInterface $baseApp, $env = 'prod') {
		self::displayErrors($env);

		//App Registrar
		return RegisterApp::instance()->addBaseApp($baseApp);
	}

	/**
	 * Display errors
	 * @param $env
	 */
	private static function displayErrors($env) {
		if ( $env == self::ENV_DEV ) {
			ini_set('display_startup_errors', 1);
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		}
	}
}
