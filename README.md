What is PHP AppLauncher?
====

- AppLauncher is an Framework which offer possibility to develop Applications and components of app. An application can contain multiple components. This framework offer possibility to create an SimpleApp, AppComponent, Security Application like Admin Panel (CMS), or App Alias.

- What is an AppAlias it is like this, function sizeof() is an alias of count(), you can do the same DemoTwoApp can be an Alias of DemoApp. Only DemoTwoApp can have other layout templates or other styles, actions, new Controllers, new Models DataBase, behavior of DemoTwoApp can be other. When you generate an App as Alias your App will Extend all Controllers and Aplication Core from DemoApp.  
	
- How do you call your First App Controller:

----------
	1. Call Simple App page -> localhost/Default/home

	[ControllerName][Action]

----------

	2. Call App Component page -> localhost/DemoComponentApp/Default/home 

	[AppName][ControllerName][Action]
	
- How you can define add an Component

---------

	require_once 'config/defines.php';
    require_once 'config/functions.php';

    use AppLauncher\Launch;
    use \AppLauncher\Action\Request;

    Launch::app(new \DemoAliasApp\DemoAliasAppController(Request::session()
    		->getVar('lang', Request::get('lang', 'char', \DemoApp\DemoAppController::DEFAULT_LANG_CODE))
    ), 'dev')
    	->addApp(new \DemoSecuredApp\DemoSecuredAppController())
    	->registerAppFacade()
    	->display();

1. Multi Language Web Framework!
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
- Actions rooting config
----

	app/DemoApp/Config/actions.ini
	------------------------------

	[en:home]
	action=DemoApp::DefaultController->defaultAction
	[es:casa]
	action=DemoApp::DefaultController->defaultAction


- Action Response
----

	public function defaultAction() {

		return array(
			'type' => 'html',
			'display' => 'index'
		);
	}

	public function countriesAction() {

		return array(
			'type' => 'html_block',
			'display' => 'countries'
		);
	}

	public function anOtherAction() {

		return array(
			'type' => 'redirect',
			'url' => 'DemoApp::DefaultController->defaultAction'
		);
	}

- DataBase configuration
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

4. Console commands
------------
	'create:app' => 'php generator.php create:app [PROJECT_NAME]',
	'create:app:controller' => 'php generator.php create:app:controller [PROJECT_NAME] [CONTROLLER_NAME]',
	'create:app:alias' => 'php generator.php create:app:alias [PROJECT_ALIAS_NAME] [PROJECT_NAME]',
	'create:secured:app' => 'php generator.php create:app [PROJECT_NAME]',
	'create:secured:app:controller' => 'php generator.php create:app:controller [PROJECT_NAME] [CONTROLLER_NAME]',
	'create:app:db:entities' => 'php generator.php create:db:entities [PROJECT_NAME] [DATABASE_CONF_NAME]',
	'create:app:db:entity' => 'php generator.php create:db:entity [PROJECT_NAME] [DATABASE_CONF_NAME] [TABLE_NAME]'

5. Form Builder based on OML ORM Manager - <a href="https://github.com/dmitrirussu/OmlManager">OML ORM Manager</a>
-------------

		use ApplauncherApp\Models\LaunchPackage\Model\Articls;
		use ApplauncherApp\Models\LaunchPackage\Model\Categories;
		use ApplauncherApp\Models\LaunchPackage\Model\KeywordTranslations;
		use HtmlFormBuilder\FormBuilder;
		use OmlManager\ORM\OmlORManager;

		$form = new FormBuilder(new Articls(),
			array(
			'fields'=> array(
				'id_category' => array(
					'keyword' => 'Categories',
					'data' => OmlORManager::oml()->model(new Categories())->fetch(),
					'key' => 'ct_key',
					'fields' => array('ct_key', 'ct_type'),
					'type' => 'select'
				),

			),
			'buttons' => array(
				'submit' => 'Save',
				'reset' => array('active' => true, 'keyword' => 'reset')
			)));


		$form->setModel(new KeywordTranslations());
		$form->setModel(new Categories());

		$this->assign('form', $form);


App Launcher Future
===
- HTML Form Builder, Form Validator