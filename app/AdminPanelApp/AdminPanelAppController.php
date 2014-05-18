<?php 
namespace AdminPanelApp;

use AppLauncher\Controller;
use AppLauncher\Action\Response;


class AdminPanelAppController extends Controller {


	public function defaultAction() {
 	
		return new Response();
	}

}
