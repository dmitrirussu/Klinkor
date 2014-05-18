<?php
/**
 * Created by Dumitru Russu.
 * Date: 15.04.2014
 * Time: 20:54
 * ${NAMESPACE}${NAME} 
 */
namespace AppLauncher;

use AppLauncher\Action\Request;
use AppLauncher\Interfaces\AppControllerInterface;
use OmlManager\ORM\OmlORManager;

abstract class Controller implements AppControllerInterface {

	const DEFAULT_LANG_CODE = 'en';

	private $langCode = self::DEFAULT_LANG_CODE;

	protected $isSecured = false;

	private $assignedTemplateVars = array();


	public function __construct($langCode = self::DEFAULT_LANG_CODE) {

		$this->setLangCode($langCode);
	}

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

	public function setLangCode($langCode) {

		$this->langCode = $langCode;
	}

	public function getLangCode() {

		return $this->langCode;
	}

	public function assign($varName, $value = null) {

		$this->assignedTemplateVars[$varName] = $value;
	}

	public function getAssignedVars() {

		return $this->assignedTemplateVars;
	}
}