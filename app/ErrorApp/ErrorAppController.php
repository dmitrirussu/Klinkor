<?php 
namespace ErrorApp;

use AppLauncher\Controller;
use AppLauncher\Action\Response;


class ErrorAppController extends Controller {

	public function __construct() {
		

		parent::__construct(self::DEFAULT_LANG_CODE);
	}

	public function defaultAction() {
		return new Response();
	}
}
