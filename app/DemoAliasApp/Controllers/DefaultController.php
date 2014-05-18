<?php 
namespace DemoAliasApp\Controllers;

use AppLauncher\Action\Response;


class DefaultController extends \DemoApp\Controllers\DefaultController {

	public function defaultAction() {

		echo 'Welcome from Alias';

		return new Response();
	}

	public function testAction() {


		echo 'Welcome';

		return new Response();
	}
}
