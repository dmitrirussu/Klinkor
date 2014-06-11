<?php 
namespace DemoSecuredApp\Controllers;

use DemoSecuredApp\DemoSecuredApp;
use AppLauncher\Action\Response;


class DefaultController extends DemoSecuredApp {


	public $isSecured = false;


	public function defaultAction() {
 	
		return parent::defaultAction();
	}

}
