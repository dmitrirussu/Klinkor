<?php 
namespace DemoSecuredApp;

use AppLauncher\Controller;
use AppLauncher\Action\Response;


class DemoSecuredAppController extends Controller {


	public	$isSecured = true;


	public function defaultAction() {
 	
		return new Response();
	}

}
