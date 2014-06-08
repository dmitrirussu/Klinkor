<?php
/**
 * Created by Dumitru Russu.
 * Date: 17.05.2014
 * Time: 17:05
 * ${NAMESPACE}${NAME} 
 */
namespace AppLauncher\Interfaces;


use AppLauncher\Action\Response;

interface AppControllerInterface {

	/**
	 * @return mixed|Response
	 */
	public function defaultAction();
	public function getDBManager();
	public function getRequest();

	/**
	 * @return AppControllerInterface
	 */
	public function getLangCode();
	public function getAssignedVars();
	public function getAssignedJavaScriptFiles();
	public function getAssignedCSSFiles();
	public function assign($varName, $value = null);
}