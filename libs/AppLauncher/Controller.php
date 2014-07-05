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
use AppLauncher\Action\Rooting;
use AppLauncher\Interfaces\AppControllerInterface;
use AppLauncher\Secure\Login;
use OmlManager\ORM\OmlORManager;

abstract class Controller implements AppControllerInterface {

	const DEFAULT_LANG_CODE = 'en';

	private $langCode = self::DEFAULT_LANG_CODE;

	protected $isSecured = false;

	private $assignedTemplateVars = array();

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
	 * Get Is secured controller
	 * @return bool
	 */
	public function isSecured() {

		return $this->isSecured;
	}
}