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

	private static $APP_NAMES = array();
	private static $INSTANCE;

	private static $IS_PROJECT_APP = false;
	private static $APP_NAME;

	private function __construct(){}
	private function __clone(){}

	public static function instance() {
		if ( empty(self::$INSTANCE) ) {

			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	/**
	 * Set Is Project Application Package
	 * @param $isProjectApp
	 * @return $this
	 */
	public function setIsProjectApp($isProjectApp) {

		self::$IS_PROJECT_APP = $isProjectApp;

		return $this;
	}

	/**
	 * get Is Project Application Package
	 * @return bool
	 */
	public static function getIsProjectApp() {

		return self::$IS_PROJECT_APP;
	}

	/**
	 * Set Included Application Package Name
	 * @param $appName
	 * @return $this
	 */
	public function setAppName($appName) {
		self::$APP_NAME = $appName;

		return $this;
	}

	/**
	 * Get included Application Package Name
	 * @return mixed
	 */
	public static function getAppName() {

		return self::$APP_NAME;
	}

	/**
	 * Add Project Extended Application
	 *
	 * @param $appName
	 * @return $this
	 */
	public function addApp($appName) {

		self::$APP_NAMES[] = $appName;

		return $this;
	}

	/**
	 * Get Project Extended Application
	 *
	 * @return array
	 */
	public function getApps() {

		return self::$APP_NAMES;
	}

	/**
	 * Include Template Block
	 * @param $tplName
	 * @param array $vars
	 * @throws Exceptions\HTMLException
	 */
	public static function block($tplName, $vars = array()) {

		$tplNotFound = false;

		//assign Var to TPL
		if ( $vars ) {

			foreach($vars AS $key => $value) {

				$$key = $value;
			}
		}

		ob_start();

		if ( self::getIsProjectApp() ) {
			$templateFile = PATH_APP.self::getAppName().'/Views/'.$tplName.self::TPL_EXT;
			if ( file_exists($templateFile) ) {
				$tplNotFound = true;

				require_once $templateFile;
			}
		}
		else {

			if ( self::$APP_NAMES ) {

				foreach(self::$APP_NAMES AS $appName) {
					$templateFile = PATH_APP.$appName.'/Views/'.$tplName.self::TPL_EXT;
					if ( file_exists($templateFile) ) {
						$tplNotFound = true;

						require_once $templateFile;

						break;
					}
				}
			}
		}

		if ( !$tplNotFound ) {

			throw new \AppLauncher\Exceptions\HTMLException('Template not found');
		}

		ob_end_flush();
	}

}
