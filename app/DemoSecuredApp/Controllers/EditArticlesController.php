<?php 
namespace DemoSecuredApp\Controllers;

use DemoSecuredApp\DemoSecuredApp;
use AppLauncher\Action\Response;


class EditArticlesController extends DemoSecuredApp {


	public $isSecured = true;


	public function defaultAction() {
 	
		return parent::defaultAction();
	}

}
