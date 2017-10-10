<?php 
namespace DemoSecureApp\Controllers;

use DemoSecureApp\DemoSecureApp;
use AppLauncher\Action\Response;


class DefaultController extends DemoSecureApp {


	public $isSecured = false;


	public function defaultAction() {
 	
		return parent::defaultAction();
	}

}
