<?php 
namespace DemoAliasApp\Controllers;

use AppLauncher\Action\Response;


class AboutController extends \DemoApp\Controllers\AboutController {

	public function defaultAction() {

		return array(
			'display' => 'default/test', 'type' => 'redirect'
		);
	}
}
