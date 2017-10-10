<?php
/**
 * Created by Dumitru Russu.
 * Date: 17.05.2014
 * Time: 15:45
 * ${NAMESPACE}${NAME} 
 */

namespace AppLauncher;

use AppLauncher\Action\Request;
use AppLauncher\Action\Routing;
use AppLauncher\Interfaces\AppControllerInterface;
use AppLauncher\Interfaces\RegisterAppInterface;

class RegisterApp implements RegisterAppInterface {

	/**
	 * @var $BASE_APP AppControllerInterface
	 */
	private static $BASE_APP;
	private static $CHILD_APPS = array();
	private static $INSTANCE = null;
	private static $CURRENT_RUNNING_APP;
	private static $CURRENT_RUNNING_APP_PARENT_APPS = array();
	private function __construct() {}

	public static function instance() {

		if ( empty(self::$INSTANCE) ) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	/**
	 * Add Base Application
	 *
	 * @param AppControllerInterface $app
	 * @return $this
	 */
	public function addBaseApp($app) {
		self::$BASE_APP = $app;

		//add rooting Actions.ini config path
		Routing::instance()->setAppName(self::$BASE_APP);
		Routing::instance()->setCurrentRunningApp(explode('\\', trim(self::$BASE_APP, '\\'))[0]);
		$this->addAppRoutingConfig($app, true);

		return $this;
	}

	/**
	 * Add Application Package to exist project
	 *
	 * @param AppControllerInterface $app
	 * @return $this|RegisterAppInterface
	 */
	public function addApp($app) {

		self::$CHILD_APPS[] = $app;
		$this->addAppRoutingConfig($app, false);

		return $this;
	}

	public function addRunningApp($app) {
		self::$CURRENT_RUNNING_APP = array('class'=> $app, 'app' => explode('\\', $app)[1]);
		$this->registerParentAppsOfCurrentRunningApp($app);
	}


	public function getCurrentRunningApp() {
		return self::$CURRENT_RUNNING_APP;
	}


	private function addCurrentRunningAppParentApps($app) {
		self::$CURRENT_RUNNING_APP_PARENT_APPS[] = $app;
	}

	public function getCurrentRunningAppPrentApps() {
		return self::$CURRENT_RUNNING_APP_PARENT_APPS;
	}


	/**
	 * Add Routing Config File
	 *
	 * @param AppControllerInterface $app
	 * @param bool $baseApp
	 * @throws RegisterAppException
	 */
	private function addAppRoutingConfig($app, $baseApp = false) {
		$appReflection = new \ReflectionClass($app);

		$fileInfo = array();
		$includedFiles = get_included_files();

		foreach($includedFiles AS $filePath) {
			if ( strpos(str_replace('/', '\\',strtoupper($filePath)), strtoupper($appReflection->getName())) !== false && file_exists($filePath) ) {
				$fileInfo = pathinfo($filePath);
				break;
			}
		}

		if ( !isset($fileInfo['dirname']) ) {

			throw new RegisterAppException('Missing Application pathInfo');
		}

		//add rooting Actions.ini config path
		Routing::instance()->setPath($fileInfo['dirname'].'/Config/');

		if ( !$baseApp ) {
			HTML::instance()->addChildApp($appReflection->getNamespaceName());
		}

		$parentAppClassName = $appReflection->getParentClass()->name;
		$appReflection = new \ReflectionClass($parentAppClassName);

		if ( !$appReflection->isAbstract() ) {
			$this->addAppRoutingConfig($parentAppClassName, $baseApp);
		}
	}

	/**
	 * Add Routing Config File
	 *
	 * @param AppControllerInterface $app
	 * @param bool $baseApp
	 * @throws RegisterAppException
	 */
	private function registerParentAppsOfCurrentRunningApp($app) {
		$appReflection = new \ReflectionClass($app);

		$this->addCurrentRunningAppParentApps(array('class' => $app, 'app' => explode('\\', $app)[1]));
		HTML::instance()->addChildApp($appReflection->getNamespaceName());

		$parentAppClassName = $appReflection->getParentClass()->name;
		$appReflection = new \ReflectionClass($parentAppClassName);

		if ( !$appReflection->isAbstract() ) {
			$this->registerParentAppsOfCurrentRunningApp($parentAppClassName);
		}
	}


	/**
	 * Get Base Application
	 *
	 * @return AppControllerInterface
	 */
	public static function getBaseApp() {

		return self::$BASE_APP;
	}


	/**
	 * Get Registered Package Applications
	 *
	 * @return array
	 */
	public static function getRegisteredApps() {
		return self::$CHILD_APPS;
	}

	/**
	 * Register Application Facade
	 * @return RegisterAppFacade
	 */
	public function registerAppFacade() {

		return new RegisterAppFacade(new Request, Routing::instance());
	}
}

class RegisterAppException extends \Exception {

}