<?php

require_once 'config/defines.php';
require_once 'config/functions.php';

use AppLauncher\Launch;
use \AppLauncher\Action\Request;

Launch::app(new \DemoAliasApp\DemoAliasAppController(Request::session()
		->getVar('lang', Request::get('lang', 'char', \DemoApp\DemoAppController::DEFAULT_LANG_CODE))
), 'dev')
	->addApp(new \DemoSecuredApp\DemoSecuredAppController())
	->registerAppFacade()
	->display();