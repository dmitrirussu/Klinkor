<?php 
namespace DemoTwoAliasApp\Controllers;

use AppLauncher\Action\Response;


class DefaultController extends \DemoAliasApp\Controllers\DefaultController {

	public function defaultAction() {

		return new Response();
	}

}
