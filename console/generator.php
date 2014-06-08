<?php

function __autoload($className) {
	$className = str_replace('\\', '/', $className);
	$phpExt = '.php';
	require_once dirname(__DIR__).'/libs/'.$className.$phpExt;
}

$action = (isset($argv[1]) ? $argv[1] : null);


$registeredCommands = array(
	'create:app' => 'php generator.php create:app [PROJECT_NAME]',
	'create:app:controller' => 'php generator.php create:app:controller [PROJECT_NAME] [CONTROLLER_NAME]',
	'create:app:alias' => 'php generator.php create:app:alias [PROJECT_NAME] [PROJECT_ALIAS_NAME]',
	'create:secured:app' => 'php generator.php create:app [PROJECT_NAME]',
	'create:secured:app:controller' => 'php generator.php create:app:controller [PROJECT_NAME] [CONTROLLER_NAME]',
	'create:app:db:entities' => 'php generator.php create:db:entities [PROJECT_NAME] [DATABASE_CONF_NAME]',
	'create:app:db:entity' => 'php generator.php create:db:entity [PROJECT_NAME] [DATABASE_CONF_NAME] [TABLE_NAME]'
);

try {
	switch($action) {
		case 'create:app': {
			$appName = (isset($argv[2]) ? $argv[2] : null);

			\AppLauncher\Generator\AppGeneratorFactory::create($appName, dirname(__DIR__))
				->createApp();

			break;
		}
		case 'create:app:alias': {
			$appName = (isset($argv[2]) ? $argv[2] : null);
			$appAliasName = (isset($argv[3]) ? $argv[3] : null);

			\AppLauncher\Generator\AppGeneratorFactory::create($appAliasName, dirname(__DIR__))
				->createAppAlias($appName);

			break;
		}
		case 'create:app:controller': {
			$appName = (isset($argv[2]) ? $argv[2] : null);
			$controllerName = (isset($argv[3]) ? $argv[3] : null);

			\AppLauncher\Generator\AppGeneratorFactory::create($appName, dirname(__DIR__))
				->createApp()
				->createAppController($controllerName)->createView($controllerName, false, true);

			break;
		}
		case 'create:secured:app': {
			$appName = (isset($argv[2]) ? $argv[2] : null);

			\AppLauncher\Generator\AppGeneratorFactory::create($appName, dirname(__DIR__))
				->createApp(true);

			break;
		}
		case 'create:secured:app:controller': {
			$appName = (isset($argv[2]) ? $argv[2] : null);
			$controllerName = (isset($argv[3]) ? $argv[3] : null);

			\AppLauncher\Generator\AppGeneratorFactory::create($appName, dirname(__DIR__))
				->createApp()
				->createAppController($controllerName, true);

			break;
		}
		case 'create:app:db:entities': {
			$projectName = (isset($argv[2]) ? $argv[2] : null);
			$dataBaseName = (isset($argv[3]) ? $argv[3] : null);

			$projectAppName = ucfirst($projectName).'App';

			if ( empty($projectName) ) {

				throw new Exception('Project name cannot be empty, see command -> '. $registeredCommands[$action]);
			}
			elseif( !is_dir(dirname(__DIR__).'/app/'.$projectAppName.'/Models/') ) {

				throw new Exception('Project directory does not exist, see command -> '. $registeredCommands[$action]);
			}


			\OmlManager\ORM\SchemaEntitiesGenerator\OmlEntitiesGeneratorFactory
				::create($dataBaseName, dirname(__DIR__).'/app/'.$projectAppName.'/Models/', $projectAppName.'\\Models\\')
				->generateSchemaEntities();

			break;
		}
		case 'create:app:db:entity': {
			$projectName = (isset($argv[2]) ? $argv[2] : null);
			$dataBaseName = (isset($argv[3]) ? $argv[3] : null);
			$entityName = (isset($argv[4]) ? $argv[4] : null);
			$projectAppName = ucfirst($projectName).'App';

			if ( empty($projectName) ) {

				throw new Exception('Project name cannot be empty, see command -> '. $registeredCommands[$action]);
			}
			elseif( !is_dir(dirname(__DIR__).'/app/'.$projectAppName.'/Models/') ) {

				throw new Exception('Project directory does not exist, see command -> '. $registeredCommands[$action]);
			}


			\OmlManager\ORM\SchemaEntitiesGenerator\OmlEntitiesGeneratorFactory
				::create($dataBaseName, dirname(__DIR__).'/app/'.$projectAppName.'/Models/', $projectAppName.'\\Models\\')
				->generateEntity($entityName);

			break;
		}
		case 'help' : {

			print_r($registeredCommands);

			break;
		}
		default : {

		throw new Exception('Your command is not registered!');
		}
	}
	echo 'Done!';
}
catch (Exception $e) {

	print_r($e->getMessage());
	print_r($e->getTrace());
}
