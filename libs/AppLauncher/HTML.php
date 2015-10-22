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
	private static $APP_PAGE_NAME;

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
	 * Set App Action Name
	 * @param $actionName
	 * @return $this
	 */
	public function setAppPageName($actionName) {
		self::$APP_PAGE_NAME = $actionName;

		return $this;
	}

	/**
	 * Get App Action Name
	 * @return mixed
	 */
	public static function getAppPageName() {

		return self::$APP_PAGE_NAME;
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

		$templateDirectory = ($isMain ? '' : self::getAppPageName().DIRECTORY_SEPARATOR);

		if ( self::getIsProjectApp() ) {

			$templateFile = PATH_APP.self::getAppName().'/Views/'. $templateDirectory.$tplName.self::TPL_EXT;
			$templateLibsFile = PATH_LIBS.self::getAppName().'/Views/'. $templateDirectory.$tplName.self::TPL_EXT;
			$templateInMainDirectory = PATH_APP.self::getAppName().'/Views/'.$tplName.self::TPL_EXT;
			$templateInMainLibsDirectory = PATH_LIBS.self::getAppName().'/Views/'.$tplName.self::TPL_EXT;

			if ( file_exists($templateFile) ) {
				$tplNotFound = true;

				require $templateFile;
			}
			elseif(file_exists($templateInMainDirectory)) {
				$tplNotFound = true;

				require $templateInMainDirectory;
			}
			elseif(file_exists($templateInMainLibsDirectory)) {
				$tplNotFound = true;

				require $templateInMainLibsDirectory;
			}
			elseif ( file_exists($templateLibsFile) ) {
				$tplNotFound = true;

				require $templateLibsFile;
			}

		}
		else {

			if ( self::$APP_NAMES ) {

				foreach(self::$APP_NAMES AS $appName) {
					$templateFile = PATH_APP.$appName.'/Views/'.$templateDirectory.$tplName.self::TPL_EXT;
					$templateLibsFile = PATH_LIBS.$appName.'/Views/'.$templateDirectory.$tplName.self::TPL_EXT;
					$templateInMainDirectory = PATH_APP.$appName.'/Views/'.$tplName.self::TPL_EXT;
					$templateInMainLibsDirectory = PATH_LIBS.$appName.'/Views/'.$tplName.self::TPL_EXT;

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
					elseif(file_exists($templateInMainLibsDirectory)) {
						$tplNotFound = true;

						require $templateInMainLibsDirectory;
						break;
					}
					elseif ( file_exists($templateLibsFile) ) {
						$tplNotFound = true;

						require $templateLibsFile;
						break;
					}
				}
			}
		}

		if ( !$tplNotFound ) {

			throw new \AppLauncher\Exceptions\HTMLException('Template not found tplName='.$tplName);
		}

		ob_end_flush();
	}

}
