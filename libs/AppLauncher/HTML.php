<?php
/**
 * Created by Dumitru Russu.
 * Date: 18.05.2014
 * Time: 16:10
 * ${NAMESPACE}${NAME} 
 */
namespace AppLauncher;



class HTML {
	const TPL_EXT = '.tpl.php';

	private static $CHILD_APP_NAMES = array();
	private static $INSTANCE;

	private static $IS_MAIN_PROJECT_APP = false;
	private static $MAIN_APP_NAME;
	private static $ACTION_VIEW_DIRECTORY;
	private static $REGISTER_APP;

	private function __construct(){}
	private function __clone(){}

	public static function instance() {
		if ( empty(self::$INSTANCE) ) {

			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}


	public function setRegisterApp(RegisterApp $registerApp) {
		self::$REGISTER_APP = $registerApp;

		return $this;
	}


	/**
	 * @return RegisterApp
	 */
	public static function getRegisterApp() {
		return self::$REGISTER_APP;
	}


	/**
	 * Set Is Project Application Package
	 * @param $isProjectApp
	 * @deprecated
	 * @return $this
	 */
	public function setIsProjectApp($isProjectApp) {

		$this->setIsMainProjectApp($isProjectApp);

		return $this;
	}
	/**
	 * Set Is Project Application Package
	 * @param $isProjectApp
	 * @return $this
	 */
	public function setIsMainProjectApp($isProjectApp) {

		self::$IS_MAIN_PROJECT_APP = $isProjectApp;

		return $this;
	}

	/**
	 * get Is Project Application Package
	 * @deprecated
	 * @return bool
	 */
	public static function getIsProjectApp() {

		return self::getIsMainProjectApp();
	}

	/**
	 * get Is Project Application Package
	 * @return bool
	 */
	public static function getIsMainProjectApp() {

		return self::$IS_MAIN_PROJECT_APP;
	}

	/**
	 * Set Included Application Package Name
	 * @param $appName
	 * @deprecated
	 * @return $this
	 */
	public function setAppName($appName) {
		$this->setMainAppName($appName);

		return $this;
	}

	/**
	 * Set Included Application Package Name
	 * @param $appName
	 * @return $this
	 */
	public function setMainAppName($appName) {
		self::$MAIN_APP_NAME[] = $appName;

		return $this;
	}

	/**
	 * Get included Application Package Name
	 * @deprecated
	 * @return mixed
	 */
	public static function getAppName() {

		return self::getMainAppName();
	}

	/**
	 * Get included Application Package Name
	 * @return mixed
	 */
	public static function getMainAppName() {

		return self::$MAIN_APP_NAME;
	}

	/**
	 * Set App Action Name
	 * @param $actionName
	 * @return $this
	 */
	public function setActionViewDirectory($actionName) {
		self::$ACTION_VIEW_DIRECTORY = $actionName;

		return $this;
	}


	/**
	 * Get App Action Name
	 * @return mixed
	 */
	public static function getActionViewDirectory() {

		return self::$ACTION_VIEW_DIRECTORY;
	}

	/**
	 * Add Project Extended Application
	 *
	 * @param $appName
	 * @return $this
	 */
	public function addChildApp($appName) {

		self::$CHILD_APP_NAMES[] = $appName;

		return $this;
	}

	/**
	 * Get Project Extended Application
	 * @deprecated
	 * @return array
	 */
	public function getApps() {

		return $this->getChildApps();
	}


	/**
	 * Get Project Extended Application
	 *
	 * @return array
	 */
	public static function getChildApps() {

		return self::$CHILD_APP_NAMES;
	}

	/**
	 * Include Template Block
	 * @param $tplName
	 * @param array $vars
	 * @param bool $isMain
	 * @throws Exceptions\HTMLException
	 */
	public static function block($tplName, $vars = array(), $isMain = false) {
		$tplNotFound = false;

		//assign Var to TPL
		if ( $vars ) {
			foreach($vars AS $key => $value) {
				$$key = $value;
			}
		}

		ob_start();
		$templateDirectory = ($isMain ? '' : self::getActionViewDirectory().DIRECTORY_SEPARATOR);

		foreach(self::getRegisterApp()->getCurrentRunningAppPrentApps() AS $app) {
			$templateFile = PATH_APP.$app['app'].'/Views/'.$templateDirectory.$tplName.self::TPL_EXT;
			$templateInMainDirectory = PATH_APP.$app['app'].'/Views/'.$tplName.self::TPL_EXT;

			if ( file_exists($templateFile) ) {
				$tplNotFound = true;

				require $templateFile;
				break;
			}
			elseif(file_exists($templateInMainDirectory)) {
				$tplNotFound = true;

				require $templateInMainDirectory;
				break;
			}
		}

		if ( !$tplNotFound ) {

			throw new \AppLauncher\Exceptions\HTMLException('Template not found tplName='.$tplName);
		}

		ob_end_flush();
	}

}
