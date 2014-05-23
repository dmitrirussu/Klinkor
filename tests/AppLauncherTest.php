<?php
/**
 * Created by Dumitru Russu.
 * Date: 23.05.2014
 * Time: 22:21
 * ${NAMESPACE}${NAME} 
 */

require_once '/../config/defines.php';
require_once '/../config/functions.php';

use AppLauncher\Launch;
use \AppLauncher\Action\Request;

class AppLauncherTest extends PHPUnit_Framework_TestCase {

	public function testLaunchApp() {

		Launch::app(new \DemoApp\DemoAppController(Request::session()
				->getVar('lang', Request::get('lang', 'char', \DemoApp\DemoAppController::DEFAULT_LANG_CODE))
		), 'dev')
			->addApp(new \AdminPanelApp\AdminPanelAppController())
			->registerAppFacade()
			->display();
	}
} 