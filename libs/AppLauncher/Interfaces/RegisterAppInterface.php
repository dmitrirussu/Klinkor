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
	 * @param AppControllerInterface $app
	 * @return self
	 */
	public function addApp(AppControllerInterface $app);

	/**
	 * @return RegisterAppFacade
	 */
	public function registerAppFacade();
}