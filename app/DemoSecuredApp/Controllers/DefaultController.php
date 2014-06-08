<?php 
namespace DemoSecuredApp\Controllers;

use DemoSecuredApp\DemoSecuredAppController;
use AppLauncher\Action\Response;


class DefaultController extends DemoSecuredAppController {


	public $isSecured = false;


	public function defaultAction() {
 	
		return parent::defaultAction();
	}

}
