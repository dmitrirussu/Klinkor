What is PHP AppLauncher?
====

- AppLauncher is an Framework which offer possibility to develop Applications and components of app. An application can contain multiple components. This framework offer possibility to create an SimpleApp, AppComponent, Security Application like Admin Panel (CMS), or App Alias.

- What is an AppAlias it is like this, function sizeof() is an alias of count(), 
but you can do the same DemoTwoApp can be an Alias of DemoApp. Only DemoTwoApp can have other layout templates or other styles, actions, new Controllers, new Models DataBase, behavior of DemoTwoApp is other. When you generate an App as Alias your App will Extend all Controllers and Aplication Core from DemoApp.  

1. Multi Language Web FrameWork!
------------


2. Include
------------
- App Config
- App Package Generator (using console)
- ORM DataBase Entities Generator (using console)
- OML ORM DataBase Manager (https://github.com/dmitrirussu/OmlManager)
- Session Manager
- Action Request Manager
- Action Routing
- Action Display Layout (Template, HTML, JSON, IMAGE)
 


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

4. Console Commands
------------
	'create:app' => 'php generator.php create:app [PROJECT_NAME]',
	'create:app:controller' => 'php generator.php create:app:controller [PROJECT_NAME] [CONTROLLER_NAME]',
	'create:app:alias' => 'php generator.php create:app:alias [PROJECT_ALIAS_NAME] [PROJECT_NAME]',
	'create:secured:app' => 'php generator.php create:app [PROJECT_NAME]',
	'create:secured:app:controller' => 'php generator.php create:app:controller [PROJECT_NAME] [CONTROLLER_NAME]',
	'create:app:db:entities' => 'php generator.php create:db:entities [PROJECT_NAME] [DATABASE_CONF_NAME]',
	'create:app:db:entity' => 'php generator.php create:db:entity [PROJECT_NAME] [DATABASE_CONF_NAME] [TABLE_NAME]'
