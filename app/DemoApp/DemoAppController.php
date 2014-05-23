<?php 
namespace DemoApp;

use AppLauncher\Controller;
use AppLauncher\Action\Response;


class DemoAppController extends Controller {


	public function defaultAction() {
 	
		return new Response();
	}

}
