<?php
/**
 * Created by Dumitru Russu.
 * Date: 05.07.2014
 * Time: 14:17
 * AppLauncher${NAME} 
 */

namespace AppLauncher;


use AppLauncher\Action\Rooting;

class Scripts {


	private static $INSTANCE;

	private static $JS_FILES = array();
	private static $CSS_FILES = array();

	const PUBLIC_DIRECTORY= '/public';
	const PUBLIC_GLOBAL_DIRECTORY = '/public/global';
	const PUBLIC_GLOBAL_CSS_DIRECTORY = '/public/global/css';
	const PUBLIC_GLOBAL_JS_DIRECTORY = '/public/global/js';

	const MACROS_URL = '{URL}';
	const SCRIP = "<script language=\"JavaScript\" type=\"text/javascript\" src=\"{URL}\"></script>\n";
	const LINK = "<link href=\"{URL}\" rel=\"stylesheet\" />\n";

	private function __construct(){}
	private function __clone() {}


	public static function instance() {
		if ( empty(self::$INSTANCE) ) {
			self::$INSTANCE = new self();
		}
		return self::$INSTANCE;
	}

	public static function addCSSFile($fileName) {

		self::$CSS_FILES[$fileName] = $fileName;
	}


	public static function addScriptJsFile($fileName) {

		self::$JS_FILES[$fileName] = $fileName;
	}

	public static function unsetAllCSSFiles() {
		self::$CSS_FILES = array();
		return true;
	}

	public static function unsetAllJSFiles() {
		self::$JS_FILES = array();
		return true;
	}

	public static function unsetCSSFile($fileName) {
		if ( isset(self::$CSS_FILES[$fileName]) ) {
			unset(self::$CSS_FILES[$fileName]);
			return true;
		}
		return false;
	}

	public static function unsetJSFile($fileName) {
		if ( isset(self::$JS_FILES[$fileName]) ) {
			unset(self::$JS_FILES[$fileName]);
			return true;
		}
		return false;
	}

	public static function getCssFiles() {

		return self::$CSS_FILES;
	}

	public static function getScriptsJs() {

		return self::$JS_FILES;
	}

	/**
	 * @return Scripts
	 */
	public static function showJs() {
		self::appDirectories(RegisterApp::instance()->getBaseApp(), $apps);
		self::appDirectories(RegisterApp::instance()->getRegisteredApps(), $apps);


		$jsScripts = '';
		$apps[] = 'global';

		if ( $apps ) {

			$apps = array_reverse($apps);
			$javascriptFiles = self::getScriptsJs();

			foreach($apps as $app) {
				if ( $javascriptFiles ) {
					foreach($javascriptFiles as $jsFile) {
						$fileNameRoot = self::PUBLIC_DIRECTORY.'/'.$app.'/'.$jsFile.'.js';
						$fileNameRootDir = dirname(__DIR__).'/..'.$fileNameRoot;

						$fileName = self::PUBLIC_DIRECTORY.'/'.$app.'/js/'.$jsFile.'.js';
						$fileDir = dirname(__DIR__).'/..'.$fileName;

						if ( file_exists($fileNameRootDir) ) {
							self::writeJs($jsScripts, DOMAIN_RESOURCES.$fileNameRoot);
						}

						if ( file_exists($fileDir) ) {
							self::writeJs($jsScripts, DOMAIN_RESOURCES.$fileName);
						}
					}
				}
			}

		}

		return print($jsScripts);
	}

	public static function showCSS() {
		self::appDirectories(RegisterApp::instance()->getBaseApp(), $apps);
		self::appDirectories(RegisterApp::instance()->getRegisteredApps(), $apps);

		$cssLinks = '';
		$apps[] = 'global';

		if ( $apps ) {
			$apps = array_reverse($apps);
			$cssFiles = self::getCssFiles();

			foreach($apps as $app) {

				if ( $cssFiles ) {
					foreach($cssFiles as $cssFile) {
						$rootFileName = self::PUBLIC_DIRECTORY.'/'.$app.'/'.$cssFile.'.css';
						$rootFileDir = dirname(__DIR__).'/..'.$rootFileName;

						$fileName = self::PUBLIC_DIRECTORY.'/'.$app.'/css/'.$cssFile.'.css';
						$fileDir = dirname(__DIR__).'/..'.$fileName;

						if (file_exists($rootFileDir)) {
							self::writeCss($cssLinks, DOMAIN_RESOURCES.$rootFileName);
						}

						if (file_exists($fileDir)) {
							self::writeCss($cssLinks, DOMAIN_RESOURCES.$fileName);
						}
					}
				}
			}
		}

		return print($cssLinks);
	}

	/**
	 * @param $script
	 * @param $fileDirectory
	 */
	private static function writeCss(&$script, $fileDirectory) {
		$script .= str_replace(self::MACROS_URL, $fileDirectory, self::LINK);
	}

	/**
	 * @param $script
	 * @param $fileDirectory
	 */
	private static function writeJs(&$script, $fileDirectory) {
		$script .= str_replace(self::MACROS_URL, $fileDirectory, self::SCRIP);
	}

	/**
	 * App Directories
	 * @param $appName
	 * @param array $apps
	 * @return bool
	 */
	private static function appDirectories($appName, &$apps = array()) {

		if ( empty($appName) ) {

			return $apps;
		}
		if ( is_array($appName) && $appName) {

			foreach($appName as $registeredApp) {
				self::appDirectories($registeredApp, $apps);
			}

			return $apps;
		}

		if ( is_object($appName) ) {
			$baseAppReflectionObject = new \ReflectionObject($appName);

			$apps[] = $baseAppReflectionObject->getNamespaceName();

			if ( $baseAppReflectionObject->getParentClass() ) {

				self::appDirectories($baseAppReflectionObject->getParentClass()->getName(), $apps);
			}
		}
		elseif($appName) {

			$baseAppReflectionClass = new \ReflectionClass($appName);

			if ( $baseAppReflectionClass->isAbstract() ) {
				return $apps;
			}

			$apps[] = $baseAppReflectionClass->getNamespaceName();

			if ( $baseAppReflectionClass->getParentClass() ) {

				self::appDirectories($baseAppReflectionClass->getParentClass()->getName(), $apps);
			}

		}
		return array_reverse($apps);
	}

}