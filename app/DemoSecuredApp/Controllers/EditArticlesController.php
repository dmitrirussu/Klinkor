<?php 
namespace DemoSecuredApp\Controllers;

use DemoSecuredApp\DemoSecuredAppController;
use AppLauncher\Action\Response;


class EditArticlesController extends DemoSecuredAppController {


	public $isSecured = true;


	public function defaultAction() {
 	
		return parent::defaultAction();
	}

}
