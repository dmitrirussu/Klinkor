<?php

require_once 'config/defines.php';
require_once 'config/functions.php';

use AppLauncher\Launch;
use \AppLauncher\Action\Request;

Launch::app(new \DemoTwoAliasApp\DemoTwoAliasAppController(Request::session()
		->getVar('lang', Request::get('lang', 'char', \DemoApp\DemoAppController::DEFAULT_LANG_CODE))
), 'dev')
	->addApp(new \AdminPanelApp\AdminPanelAppController())
	->registerAppFacade()
	->display();