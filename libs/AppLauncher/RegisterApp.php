<?php
/**
 * Created by Dumitru Russu.
 * Date: 17.05.2014
 * Time: 15:45
 * ${NAMESPACE}${NAME} 
 */

namespace AppLauncher;

use AppLauncher\Action\Request;
use AppLauncher\Action\Rooting;
use AppLauncher\Interfaces\AppControllerInterface;
use AppLauncher\Interfaces\RegisterAppInterface;

class RegisterApp implements RegisterAppInterface {

	/**
	 * @var $BASE_APP AppControllerInterface
	 */
	private static $BASE_APP;
	private static $APPS = array();
	private static $INSTANCE = null;

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
	public function addBaseApp(AppControllerInterface $app) {
		self::$BASE_APP = $app;

		//add rooting Actions.ini config path
		Rooting::instance()->setLangCode(self::$BASE_APP->getLangCode())
			->setAppName(get_class(self::$BASE_APP));

		$this->addAppRootingConfig($app, true);

		return $this;
	}

	/**
	 * Add Rooting Config File
	 *
	 * @param AppControllerInterface $app
	 * @param bool $baseApp
	 * @throws RegisterAppException
	 */
	private function addAppRootingConfig(AppControllerInterface $app, $baseApp = false) {
		$appReflection = new \ReflectionObject($app);

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
		Rooting::instance()->setPath($fileInfo['dirname'].'/Config/');

		if ( $baseApp ) {

			HTML::instance()->addApp($appReflection->getNamespaceName());
		}

		$parentAppClassName = $appReflection->getParentClass()->name;
		$appReflection = new \ReflectionClass($parentAppClassName);

		if ( !$appReflection->isAbstract() ) {

			$app = new $parentAppClassName();

			$this->addAppRootingConfig($app, $baseApp);
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
	 * Add Application Package to exist project
	 *
	 * @param AppControllerInterface $app
	 * @return $this|RegisterAppInterface
	 */
	public function addApp(AppControllerInterface $app) {

		self::$APPS[] = $app;

		$this->addAppRootingConfig($app);

		return $this;
	}

	/**
	 * Get Registered Package Applications
	 *
	 * @return array
	 */
	public static function getRegisteredApps() {

		return self::$APPS;
	}

	/**
	 * Register Application Facade
	 * @return RegisterAppFacade
	 */
	public function registerAppFacade() {

		return new RegisterAppFacade(new Request, Rooting::instance());
	}
}

class RegisterAppException extends \Exception {

}