<?php
/**
 * Created by Dumitru Russu.
 * Date: 15.04.2014
 * Time: 20:54
 * ${NAMESPACE}${NAME} 
 */
namespace AppLauncher;

use AppLauncher\Action\Request;
use AppLauncher\Action\Response;
use AppLauncher\Interfaces\AppControllerInterface;
use OmlManager\ORM\OmlORManager;

abstract class Controller implements AppControllerInterface {

	const DEFAULT_LANG_CODE = 'en';

	private $langCode = self::DEFAULT_LANG_CODE;

	protected $isSecured = false;

	private $assignedTemplateVars = array();

	private $errorMessages = array();
	private $successMessages = array();

	private $breadCrumbs = array();
	private $addBradCrumbsEnabled = true;

	public function __construct($langCode = self::DEFAULT_LANG_CODE) {

		$this->setLangCode($langCode);
	}

	/**
	 * @return mixed|Response
	 */
	abstract public function defaultAction();

	/**
	 * @return OmlORManager
	 */
	public function getDBManager() {

		return new OmlORManager();
	}

	/**
	 * @return Request
	 */
	public function getRequest() {

		return new Request();
	}

	/**
	 * @param $langCode
	 */
	public function setLangCode($langCode) {

		$this->langCode = $langCode;
	}

	/**
	 * @return string
	 */
	public function getLangCode() {

		return $this->langCode;
	}

	/**
	 * @param $varName
	 * @param null $value
	 * @return $this
	 */
	public function assign($varName, $value = null) {

		$this->assignedTemplateVars[$varName] = $value;

		return $this;
	}

	/**
	 * Add Java Script File
	 * @param $fileName
	 * @return $this
	 */
	public function addJScriptFile($fileName) {
		Scripts::instance()->addScriptJsFile($fileName);

		return $this;
	}

	/**
	 * Add CSS File
	 * @param $fileName
	 * @return $this
	 */
	public function addCSSFile($fileName) {

		Scripts::instance()->addCSSFile($fileName);

		return $this;
	}

	/**
	 * Get Assigned Java Script File
	 * @return array
	 */
	public function getAssignedJavaScriptFiles() {

		return Scripts::instance()->getScriptsJs();
	}

	/**
	 * Get Assigned CSS Files
	 * @return array
	 */
	public function getAssignedCSSFiles() {

		return Scripts::instance()->getCssFiles();
	}

	/**
	 * Get Assigned Vars
	 * @return array
	 */
	public function getAssignedVars() {

		return $this->assignedTemplateVars;
	}

	/**
	 * Add Error Message
	 * @param $message
	 */
	public function addErrorMessage($message) {
		$this->errorMessages[] = (is_array($message) ? implode('<br />', $message) : $message);
		$this->getRequest()->session()->setVar('errors', $this->errorMessages);
	}

	/**
	 * Get Error Messages
	 * @return array
	 */
	public function getErrorMessages() {
		return $this->getRequest()->session()->getVar('errors', $this->errorMessages);
	}

	/**
	 * Add Success Message
	 * @param $message
	 */
	public function addSuccessMessage($message) {
		$this->successMessages[] = (is_array($message) ? implode('<br />', $message) : $message);
		$this->getRequest()->session()->setVar('successMessage', $this->successMessages);
	}

	/**
	 * Get Success Messages
	 * @return array
	 */
	public function getSuccessMessages() {
		return $this->getRequest()->session()->getVar('successMessage', $this->successMessages);
	}

	/**
	 * Destroy Error and Success Messages
	 * @return bool
	 */
	public function destroyMessages() {
		return $this->getRequest()->session()->unsetVar('errors') == $this->getRequest()->session()->unsetVar('successMessage');
	}

	/**
	 * Get Is secured controller
	 * @return bool
	 */
	public function isSecured() {

		return $this->isSecured;
	}

	/**
	 * Add Bread Crumb
	 * @param $name
	 * @param string $action
	 * @param bool $isActive
	 * @return $this
	 */
	public function addBreadCrumb($name, $action = '', $isActive = null) {

		if ( $this->addBradCrumbsEnabled ) {
			$this->breadCrumbs[] = array(
				'name' => $name,
				'action' => $action,
				'is_active' => $isActive
			);
		}

		return $this;
	}

	/**
	 * Add Bread Crumb
	 * @param $name
	 * @param string $action
	 * @param bool $isActive
	 * @return $this
	 */
	public function addEndOfBreadCrumb($name, $action = '', $isActive = null) {

		$this->addBreadCrumb($name, $action, $isActive);
		$this->addBradCrumbsEnabled = false;

		return $this;
	}

	/**
	 * Get Bread Crumbs
	 * @return array
	 */
	public function getBreadCrumbs() {
		return $this->breadCrumbs;
	}
}