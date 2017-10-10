<?php
/**
 * Created by Dumitru Russu.
 * Date: 05.07.2014
 * Time: 14:17
 * AppLauncher${NAME}
 */

namespace AppLauncher;


use AppLauncher\Action\Routing;
use BackOfficeApp\Models\PopaccountingPackage\Model\AppSettings;

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

		if  ( isset(self::$CSS_FILES['styles']) ) {
			self::$CSS_FILES[] = self::$CSS_FILES['styles'];
			unset(self::$CSS_FILES['styles']);
		}
		return self::$CSS_FILES;
	}

	public static function getScriptsJs() {

		return self::$JS_FILES;
	}

	/**
	 * @return Scripts
	 */
	public static function showJs() {
		require_once PATH_LIBS."minify/vendor/autoload.php";
		$minifyJS = new \MatthiasMullie\Minify\JS();
		$domain = (APP_CURRENT_FOLDER ? DOMAIN_FILE_RESOURCES : '');
		$acceptGZCompression = strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;
		$jsScripts = '';
		$apps = array_reverse(RegisterApp::instance()->getCurrentRunningAppPrentApps());
		array_unshift($apps, array('app' => 'global'));
		$minFileName = '';
		$minFileExists = 1;
		try {
			$scriptMinify = AppSettings::getConstValue('SCRIPTS_MINIFY');
		}
		catch (\Exception $e) {
			$scriptMinify = 0;
		}
		if ( $apps ) {
			$javascriptFiles = self::getScriptsJs();

			foreach($apps as $app) {
				if ( $javascriptFiles ) {
					foreach($javascriptFiles as $jsFile) {
						$fileNameRoot = self::PUBLIC_DIRECTORY.'/'.$app['app'].'/'.$jsFile.'.js';
						$fileNameRootDir = dirname(__DIR__).'/..'.$fileNameRoot;

						$fileName = self::PUBLIC_DIRECTORY.'/'.$app['app'].'/js/'.$jsFile.'.js';
						$fileDir = dirname(__DIR__).'/..'.$fileName;

						if ( is_file($fileNameRootDir) ) {
							if ( strpos($fileNameRoot, 'min') === false && $scriptMinify) {
								$minFileName .= str_replace(array('=', '/', '\\', '.'),  '', $jsFile);
								$minifyJS->add(PATH_PUBLIC.str_replace('/public/', '', $fileNameRoot));
							}
							else {
								self::writeJs($jsScripts, $domain.$fileNameRoot);
							}
						}

						if ( is_file($fileDir) ) {
							if ( strpos($fileDir, 'min') === false && $scriptMinify) {
								$minFileName .= str_replace(array('=', '/', '\\', '.', '-'),  '_', $jsFile);
								$minifyJS->add( PATH_PUBLIC . str_replace( '/public/', '', $fileName ) );
							}
							else {
								self::writeJs($jsScripts, $domain.$fileName);
							}
						}
					}
				}
			}

			if ( $scriptMinify ) {
				// Check for buggy versions of Internet Explorer
				if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
				    preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
					$version = floatval($matches[1]);

					if ($version < 6) {
						$minFileExists = $acceptGZCompression = 0;
					}

					if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1')) {
						$minFileExists = $acceptGZCompression = 0;
					}
				}


				$minFileName = md5($minFileName);
				$minFileName = strtolower("global/js/min/{$minFileName}{$acceptGZCompression}.js");
				$minFileExists = ($minFileExists && is_file(PATH_PUBLIC.$minFileName) ?  true : false);

				if ( !$minFileExists ) {
					if ( !is_dir(PATH_PUBLIC.'global/js/min/') ) {
						mkdir(PATH_PUBLIC.'global/js/min/', true);
						chmod(PATH_PUBLIC.'global/js/min/', 755);
					}
					($acceptGZCompression ? $minifyJS->gzip(PATH_PUBLIC.$minFileName) : $minifyJS->minify(PATH_PUBLIC.$minFileName));
				}

				self::writeJs($jsScripts, "{$domain}/public/global/scripts.php?file={$minFileName}&compression={$acceptGZCompression}");
			}
		}

		return print($jsScripts);
	}

	public static function showCSS() {
		require_once PATH_LIBS."minify/vendor/autoload.php";
		$minifyCSS = new \MatthiasMullie\Minify\CSS();
		$acceptGZCompression = strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;
		$domain = (APP_CURRENT_FOLDER ? DOMAIN_FILE_RESOURCES : '');
		$minFileName = '';
		$minFileExists = 1;
		$cssLinks = '';
		$apps = array_reverse(RegisterApp::instance()->getCurrentRunningAppPrentApps());
		array_unshift($apps, array('app' => 'global'));
		try {
			$scriptMinify = AppSettings::getConstValue('SCRIPTS_MINIFY');
		}
		catch (\Exception $e) {
			$scriptMinify = 0;
		}

		if ( $apps ) {
			$cssFiles = self::getCssFiles();

			foreach($apps as $app) {
				if ( $cssFiles ) {
					foreach($cssFiles as $cssFile) {
						$rootFileName = self::PUBLIC_DIRECTORY.'/'.$app['app'].'/'.$cssFile.'.css';
						$rootFileDir = dirname(__DIR__).'/..'.$rootFileName;

						$fileName = self::PUBLIC_DIRECTORY.'/'.$app['app'].'/css/'.$cssFile.'.css';
						$fileDir = dirname(__DIR__).'/..'.$fileName;

						if (is_file($rootFileDir)) {
							if ( $scriptMinify ) {
								$minFileName .= str_replace(array('=', '/', '\\', '.'),  '', $cssFile);
								$minifyCSS->add( PATH_PUBLIC . str_replace( '/public/', '', $rootFileName ) );
							}
							else {
								self::writeCss($cssLinks, $domain.$rootFileName);
							}
						}

						if (is_file($fileDir)) {
							if ( $scriptMinify ) {
								$minFileName .= str_replace(array('=', '/', '\\', '.'),  '', $cssFile);
								$minifyCSS->add( PATH_PUBLIC . str_replace( '/public/', '', $fileName ) );
							}
							else {
								self::writeCss($cssLinks, $domain.$fileName);
							}
						}
					}
				}
			}


			if ( $scriptMinify ) {
				// Check for buggy versions of Internet Explorer
				if ( ! strstr( $_SERVER['HTTP_USER_AGENT'], 'Opera' ) &&
				     preg_match( '/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches )
				) {
					$version = floatval( $matches[1] );

					if ( $version < 6 ) {
						$minFileExists = $acceptGZCompression = 0;
					}

					if ( $version == 6 && ! strstr( $_SERVER['HTTP_USER_AGENT'], 'EV1' ) ) {
						$minFileExists = $acceptGZCompression = 0;
					}
				}

				$minFileName   = md5($minFileName);
				$minFileName   = strtolower( "global/css-min/{$minFileName}{$acceptGZCompression}.css" );
				$minFileExists = ( $minFileExists && is_file( PATH_PUBLIC . $minFileName ) ? true : false );

				if ( !$minFileExists ){
					if ( !is_dir(PATH_PUBLIC.'global/css-min/') ) {
						mkdir(PATH_PUBLIC.'global/css-min/', true);
						chmod(PATH_PUBLIC.'global/css-min/', 755);
					}
					( $acceptGZCompression ? $minifyCSS->gzip( PATH_PUBLIC . $minFileName ) : $minifyCSS->minify( PATH_PUBLIC . $minFileName ) );
				}

				self::writeCss( $cssLinks, "{$domain}/public/global/css-min/scripts.php?file={$minFileName}&compression={$acceptGZCompression}" );
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
}