<?php
/**
 * Created by Dumitru Russu.
 * Date: 09.05.2014
 * Time: 10:04
 * AppLauncher\Generator${NAME} 
 */

namespace AppLauncher\Generator;


class AppGenerator {

	private $appName;
	private $path;

	public function __construct($appName, $path) {

		if ( empty($appName) ) {

			throw new AppGeneratorException('AppName cannot be empty');
		}
		elseif( !ctype_alpha($appName) ) {

			throw new AppGeneratorException('AppName cannot be AlphaNumeric');
		}

		$this->appName = ucfirst($appName).'App';
		$this->path = $path;
	}

	public function createApp($isSecured = false) {

		$this->createAppFolder();
		$this->createBaseAppClass($isSecured);
		$this->createAppController('Default', $isSecured);
		//create tpl view directory
		$this->createView('default');
		return $this;
	}

	public function createAppAlias($appAlias) {
		$this->createAppFolder();
		$this->createBaseAppClass(false, $appAlias);

		$appAlias = ucfirst($appAlias) . 'App';

		if ( is_dir($this->path.'/app/'.$appAlias.'/Controllers/') ) {
			$dir = opendir($this->path.'/app/'.$appAlias.'/Controllers/');

			while (false !== ($entry = readdir($dir))) {

				if ( strlen($entry) > 2) {
					$pathInfo = pathinfo($entry);
					$controllerName = str_replace('Controller', '', $pathInfo['filename']);

					$this->createAppController($controllerName, false, $appAlias);

					//create tpl view directory
					$this->createView($controllerName, true);
				}
			}

			closedir($dir);

			return $this;
		}

		throw new AppGeneratorException('Project not found');
	}

	/**
	 * Create Application Folder
	 */
	private function createAppFolder() {

		$public = $this->path.'/public/' . $this->appName;
		$appPath = $this->path.'/app/' . $this->appName;

		if ( !is_dir($appPath) ) {

			mkdir($appPath, 777, true);
			mkdir($appPath.'/Config', 777, true);
			mkdir($appPath.'/Controllers', 777, true);
			mkdir($appPath.'/Models', 777, true);
			mkdir($appPath.'/Views', 777, true);
			mkdir($public.'/js', 777, true);
			mkdir($public.'/css', 777, true);
			mkdir($public.'/img', 777, true);
		}

		return $this;
	}

	/**
	 * Create Base Application Class
	 */
	private function createBaseAppClass($isSecured = false, $appAliasName = '') {

		$appControllerName = $this->appName.'Controller';

		$appAliasName = ucfirst($appAliasName);
		$appAliasFolder = $appAliasName.'App';

		if ( file_exists($this->path .'/app/'. $this->appName . '/'.$appControllerName.'.php') ) {

			return true;
		}

		if ( !file_exists($this->path .'/app/'. $this->appName.'/Config/actions.ini') ) {

			file_put_contents($this->path .'/app/'. $this->appName.'/Config/actions.ini', '');
		}

		$f = fopen($this->path .'/app/'. $this->appName . '/'.$appControllerName.'.php', 'w');

		//namespace
		$namespace = str_replace(AppGeneratorConf::_NAMESPACE, $this->appName, AppGeneratorConf::$_NAMESPACE);
		//namespace use

		$use = null;
		$method = null;

		$attributes = null;
		if ( empty($appAliasName) ) {

			if ( $isSecured ) {

				$attributes = "\tpublic	\$isSecured = true;\n\n\n";
			}

			$use = str_replace(AppGeneratorConf::_NAMESPACE, 'AppLauncher\Controller', AppGeneratorConf::$_USE);
			$method .= str_replace(
				array(AppGeneratorConf::METHOD_NAME,
					AppGeneratorConf::METHOD_PARAMS,
					AppGeneratorConf::CONTENT),
				array('__construct',
					'$langCode = self::DEFAULT_LANG_CODE',
					"\n\t\t\$this->addCSSFile('styles');\n\t\t\$this->addJScriptFile('jq/jquery');\n\t\t\$this->addJScriptFile('scripts');
					\n\t\tparent::__construct(\$langCode);"), AppGeneratorConf::$_METHOD);

			$method .= str_replace(array(AppGeneratorConf::METHOD_NAME, AppGeneratorConf::METHOD_PARAMS, AppGeneratorConf::CONTENT), array('defaultAction', '', "\n\t\treturn new Response();"), AppGeneratorConf::$_METHOD);
			$extend = 'extends Controller';

			file_put_contents($this->path .'/public/'. $this->appName .'/js/scripts.css', '');
			file_put_contents($this->path .'/public/'. $this->appName .'/css/styles.css', '');
		}
		else {

			$appAliasName = $appAliasName.'AppController';

			$use = str_replace(AppGeneratorConf::_NAMESPACE, $appAliasFolder.'\\'.$appAliasName, AppGeneratorConf::$_USE);
			$extend = 'extends '.$appAliasName;
		}

		$use .= str_replace(AppGeneratorConf::_NAMESPACE, 'AppLauncher\Action\Response', AppGeneratorConf::$_USE);

		$class = str_replace(
			array(AppGeneratorConf::CLASS_NAME, AppGeneratorConf::EXTEND_CLASS, AppGeneratorConf::CONTENT),
			array($appControllerName, $extend, $attributes . $method), AppGeneratorConf::$_CLASS);


		fwrite($f, "<?php \n".$namespace . $use . $class);

		return fclose($f);
	}

	/**
	 * Create Application
	 * @param $controllerName
	 * @param bool $isSecured
	 * @param string $appAlias
	 * @throws AppGeneratorException
	 * @return $this
	 */
	public function createAppController($controllerName, $isSecured = false, $appAlias = '') {

		if ( empty($controllerName) ) {

			throw new AppGeneratorException('Controller name cannot be empty');
		}

		$controllerName = ucfirst($controllerName);
		$controllerName .='Controller';
		$appControllerName = $this->appName.'Controller';
		$method = null;
		$use = null;

		if ( file_exists($this->path .'/app/'. $this->appName . '/Controllers/'.$controllerName.'.php') ) {

			return true;
		}

		if ( $appAlias ) {

			$appControllerName = '\\'.$appAlias.'\\Controllers\\'.$controllerName;
		}
		else {
			$use = str_replace(AppGeneratorConf::_NAMESPACE, $this->appName.'\\'.$appControllerName, AppGeneratorConf::$_USE);
			$method = str_replace(array(AppGeneratorConf::METHOD_NAME, AppGeneratorConf::METHOD_PARAMS, AppGeneratorConf::CONTENT), array('defaultAction', '', "\n\t\treturn parent::defaultAction();"), AppGeneratorConf::$_METHOD);
		}


		$f = fopen($this->path .'/app/'. $this->appName . '/Controllers/'.$controllerName.'.php', 'w');


		//namespace
		$namespace = str_replace(AppGeneratorConf::_NAMESPACE, $this->appName.'\\Controllers', AppGeneratorConf::$_NAMESPACE);
		//namespace use
		$use .= str_replace(AppGeneratorConf::_NAMESPACE, 'AppLauncher\Action\Response', AppGeneratorConf::$_USE);

		$attributes = null;

		//add is secured attribute on true
		if ( $isSecured) {
			$attributes = "\tpublic \$isSecured = true;\n\n\n";

			if ( $isSecured && $controllerName === 'DefaultController' ) {
				$attributes = "\tpublic \$isSecured = false;\n\n\n";
			}
		}


		$class = str_replace(
			array(AppGeneratorConf::CLASS_NAME, AppGeneratorConf::EXTEND_CLASS, AppGeneratorConf::CONTENT),
			array($controllerName, 'extends ' . $appControllerName, $attributes . $method), AppGeneratorConf::$_CLASS);

		fwrite($f, "<?php \n". $namespace . $use . $class);

		fclose($f);

		return $this;
	}


	public function createView($tplDirectoryName, $isAlias = false, $createTplOnly = false) {
		$tplDirectoryName = strtolower($tplDirectoryName);

		if ( !is_dir($this->path.'/app/' . $this->appName.'/Views/'.$tplDirectoryName) ) {
			mkdir($this->path.'/app/' . $this->appName.'/Views/'.$tplDirectoryName, 0777, true);
		}

		if ( !$createTplOnly ) {
			if ( !file_exists($this->path.'/app/' . $this->appName.'/Views/index.tpl.php') && !$isAlias) {

				file_put_contents($this->path.'/app/' . $this->appName.'/Views/index.tpl.php', '
<?php \AppLauncher\HTML::block(\'header\', array(\'javaScriptFiles\' => $javaScriptFiles, \'cssFiles\' => $cssFiles)); ?>
<?php \AppLauncher\HTML::block($tplName, $globalVars); ?>
<?php \AppLauncher\HTML::block(\'footer\'); ?>
');

				file_put_contents($this->path.'/app/' . $this->appName.'/Views/header.tpl.php', "
<!DOCTYPE html>
<html lang=\"en\">
<head>
	<meta charset=\"UTF-8\">
	<title>{$this->appName}</title>
	<?php if (\$cssFiles): ?>
		<?php foreach(\$cssFiles AS \$file): ?>
			<link rel=\"stylesheet\" type=\"text/css\" href=\"/public/global/css/<?php echo(\$file); ?>.css\" />
		<?php endforeach; ?>
	<?php endif; ?>

<?php if (\$javaScriptFiles): ?>
	<?php foreach(\$javaScriptFiles AS \$file): ?>
		<script type=\"text/javascript\" language=\"javascript\" src=\"/public/global/js/<?php echo(\$file); ?>.js\"></script>
	<?php endforeach; ?>
<?php endif; ?>
</head>
<body>
<div class=\"header\"></div>
<div class=\"container\">
");

				file_put_contents($this->path.'/app/' . $this->appName.'/Views/footer.tpl.php', '
</div>
<div class="footer"></div>
</body>
</html>
			');
			}
		}


		if ( !file_exists($this->path.'/app/' . $this->appName.'/Views/'.$tplDirectoryName.'/index.tpl.php') && !$isAlias) {

			file_put_contents($this->path.'/app/' . $this->appName.'/Views/'.$tplDirectoryName.'/index.tpl.php',
				"<h1>Welcome to {$tplDirectoryName} Page</h1>");
		}
		else {

			return false;
		}

		return true;
	}
}

class AppGeneratorException extends \Exception {

}