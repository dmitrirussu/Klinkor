<?php 
namespace ErrorApp\Controllers;

use ErrorApp\ErrorApp;
use AppLauncher\Action\Response;


class DefaultController extends ErrorApp {


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
