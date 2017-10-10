<?php
/**
 * Created by Dumitru Russu.
 * Date: 17.05.2014
 * Time: 15:45
 * ${NAMESPACE}${NAME} 
 */

namespace AppLauncher\Interfaces;

use AppLauncher\RegisterAppFacade;

interface RegisterAppInterface {

	/**
	 * @param $app
	 * @return self
	 */
	public function addApp($app);
	public function addRunningApp($app);
	public function getCurrentRunningApp();
	public function getCurrentRunningAppPrentApps();

	/**
	 * @return RegisterAppFacade
	 */
	public function registerAppFacade();
}