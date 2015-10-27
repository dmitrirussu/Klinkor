<?php
/**
 * Created by Dumitru Russu.
 * Date: 17.05.2014
 * Time: 17:05
 * ${NAMESPACE}${NAME} 
 */
namespace AppLauncher\Interfaces;


use AppLauncher\Action\Request;
use AppLauncher\Action\Response;

interface AppControllerInterface {

	/**
	 * @return Response
	 */
	public function defaultAction();
	public function getDBManager();

	/**
	 * @return Request
	 */
	public function getRequest();

	/**
	 * @return AppControllerInterface
	 */
	public function isSecured();
	public function isLogged();
	public function login($email, $password, $hashedPassword, $remember = false);
	public function logout();
	public function getLangCode();
	public function getAssignedVars();
	public function getAssignedJavaScriptFiles();
	public function getAssignedCSSFiles();
	public function addErrorMessage($message);
	public function getErrorMessages();
	public function addSuccessMessage($message);
	public function getSuccessMessages();
	public function addBreadCrumb($name, $action = '', $isActive = true);
	public function addEndOfBreadCrumb($name, $action = '', $isActive = null);
	public function getBreadCrumbs();
	public function assign($varName, $value = null);
	public function destroyMessages();
}