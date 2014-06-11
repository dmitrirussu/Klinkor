<?php

require_once 'config/defines.php';
require_once 'config/functions.php';

use AppLauncher\Launch;
use \AppLauncher\Action\Request;

Launch::app(new \DemoAliasApp\DemoAliasApp(Request::session()
		->getVar('lang', Request::get('lang', 'char', \DemoAliasApp\DemoAliasApp::DEFAULT_LANG_CODE))
), 'dev')
	->addApp(new \DemoSecuredApp\DemoSecuredApp())
	->registerAppFacade()
	->display();