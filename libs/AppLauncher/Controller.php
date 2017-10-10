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
use AppLauncher\Secure\Login;
use AppLauncher\Secure\User;
use AppLauncher\Utils\AppLog;
use OmlManager\ORM\OmlORManager;

abstract class Controller implements AppControllerInterface {

	const DEFAULT_LANG_CODE = 'en';

	private $langCode = self::DEFAULT_LANG_CODE;

	protected $isSecured = false;
	protected $forceHTTPSRequest = false;

	private $assignedTemplateVars = array();

	private $errorMessages = array();

	private $successMessages = array();
	private $breadCrumbs = array();
	private $addBradCrumbsEnabled = true;

	public function __construct($langCode = self::DEFAULT_LANG_CODE) {

		$this->setLangCode($langCode);

	}

	public function hasForceHTTPSRequest() {
		return $this->forceHTTPSRequest;
	}

	public function hasForceHTTPRequest() {
		return !$this->forceHTTPSRequest;
	}


	public function doRedirectToHTTPS() {
		if ( $this->isHttpRequest() && $this->forceHTTPSRequest ) {
			header("Location: {$this->getHttpsUri()}");
		}
	}


	public function doRedirectToHTTP() {
		if ( $this->isHttpRequest() && $this->forceHTTPSRequest ) {
			header("Location: {$this->getHttpUri()}");
		}
	}

	public function isHttpRequest() {
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? false : true);
	}

	public function isHttpsOn() {
		return
			(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
			|| $_SERVER['SERVER_PORT'] == 443;
	}

	public function getHttpsUri() {
		return 'https:'.DOMAIN_RESOURCES.$_SERVER['REQUEST_URI'];
	}

	public function getHttpUri() {
		return 'http:'.DOMAIN_RESOURCES.$_SERVER['REQUEST_URI'];
	}

	/**
	 * @return mixed|Response
	 */
	public function defaultAction() {}

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
	 * @return bool
	 */
	public function isLogged() {

		$email = Request::session()->getVar('email', false);
		$password = Request::session()->getVar('password', false);

		if ( empty($email) || empty($password) ) {

			return false;
		}

		return true;
	}

	/**
	 * Login user by nickname and his Password
	 * @param $email
	 * @param $password
	 * @param $remember
	 * @return bool
	 */
	public function login($email, $password, $hashedPassword, $remember = false) {

		if ( empty($email) || empty($password) || !Login::verify($password, $hashedPassword)) {
			return false;
		}

		$this->getRequest()->session()->setVar('email', $email);
		$this->getRequest()->session()->setVar('password', $password);

		//Set is Remember Life Time until 31 days
		if ( $remember ) {
			$this->getRequest()->session()->setSessionLifeTime((3600 * 24) * 31);
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function logout() {
		return $this->getRequest()->session()->destroyAll();
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
     * @param int $hideAfter
     */
	public function addErrorMessage($message, $hideAfter = 0) {
        $message = (is_array($message) ? implode('<br />', $message) : $message);
        $message = array(
            'message' => $message,
            'hide_after' => $hideAfter
        );
        $this->errorMessages[] = $message;
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
     * @param int $hideAfter
     */
	public function addSuccessMessage($message, $hideAfter = 0) {
        $message = (is_array($message) ? implode('<br />', $message) : $message);
        $message = array(
            'message' => $message,
            'hide_after' => $hideAfter
        );
        $this->successMessages[] = $message;
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
	 * @return Utils\AppLogInterface
	 */
	public function logger() {
		return AppLog::getInstance();
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