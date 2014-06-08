<?php 
namespace ErrorApp\Controllers;

use ErrorApp\ErrorAppController;
use AppLauncher\Action\Response;


class DefaultController extends ErrorAppController {


	public function defaultAction() {
		/**
		 * @var $exception \Exception
		 */
		$exception = unserialize($this->getRequest()->session()->getVar('exception'));

		$this->assign('exceptionMessage', $exception->getMessage());
		$this->assign('exceptionTrace', $exception->getTrace());

		return parent::defaultAction();
	}
}
