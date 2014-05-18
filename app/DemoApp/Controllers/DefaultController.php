<?php 
namespace DemoApp\Controllers;

use DemoApp\DemoAppController;
use AppLauncher\Action\Response;


class DefaultController extends DemoAppController {


	public function defaultAction() {

		return array('display' => 'index', 'type' => 'html');
	}

}
