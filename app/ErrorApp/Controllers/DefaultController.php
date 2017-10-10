<?php 
namespace ErrorApp\Controllers;

use ErrorApp\ErrorApp;
use AppLauncher\Action\Response;


class DefaultController extends ErrorApp {


	public function defaultAction() {
		/**
		 * @var $exception \Exception
		 */
		$exception = $this->getRequest()->session()->getVar('exception');

		if ( $exception ) {
			$this->assign('exceptionMessage', $exception->getMessage());
			$this->assign('exceptionTrace', $exception->getTrace());
		}
		else {
			$this->assign('exceptionMessage', 'Missing Controller');
			$this->assign('exceptionTrace', array());
		}

		return parent::defaultAction();
	}
}
