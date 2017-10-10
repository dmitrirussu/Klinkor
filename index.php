<?php

require_once 'config/defines.php';
require_once 'config/functions.php';

use AppLauncher\Launch;
use \AppLauncher\Action\Request;

Launch::app('\DemoAliasApp\DemoAliasApp', 'dev')
	->addApp('\DemoSecureApp\DemoSecureApp')
	->registerAppFacade()
	->display();