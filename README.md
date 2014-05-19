What is AppLauncher?
====

1. Multi Language Web FrameWork!
------------


2. Include
------------
- App Config
- App Console Generator
- ORM DataBase Entities Console Generator
- OML DataBase Manager (https://github.com/dmitrirussu/OmlManager)

3. Configs
-----------
- Actions Config
----

	app/DemoApp/Config/actions.ini
	------------------------------

	[en:home]
	action=DemoApp::DefaultController->defaultAction
	[es:casa]
	action=DemoApp::DefaultController->defaultAction


- DataBase Configuration
----

	config/databases.ini
	--------------------

	[default]
    driver = pdo_mysql;
    host = localhost;
    db_name = '';
    user = 'root';
    password = '';
    port = '';
    [db_launch]
    driver = pdo_mysql;
    host = localhost;
    db_name = launch;
    user = root;
    password = ;
    port = '';

4. Commands
------------
	'create:app' => 'php generator.php create:app [PROJECT_NAME]',
	'create:app:controller' => 'php generator.php create:app:controller [PROJECT_NAME] [CONTROLLER_NAME]',
	'create:app:alias' => 'php generator.php create:app:alias [PROJECT_ALIAS_NAME] [PROJECT_NAME]',
	'create:secured:app' => 'php generator.php create:app [PROJECT_NAME]',
	'create:secured:app:controller' => 'php generator.php create:app:controller [PROJECT_NAME] [CONTROLLER_NAME]',
	'create:app:db:entities' => 'php generator.php create:db:entities [PROJECT_NAME] [DATABASE_CONF_NAME]',
	'create:app:db:entity' => 'php generator.php create:db:entity [PROJECT_NAME] [DATABASE_CONF_NAME] [TABLE_NAME]'
