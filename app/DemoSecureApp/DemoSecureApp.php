<?php 
namespace DemoSecureApp;

use AppLauncher\Controller;
use AppLauncher\Action\Response;


class DemoSecureApp extends Controller {


	public	$isSecured = true;


	public function __construct($langCode = self::DEFAULT_LANG_CODE) {
 	
		$this->addCSSFile('styles');
		$this->addJScriptFile('jq/jquery');
		$this->addJScriptFile('scripts');
					
		parent::__construct($langCode);
	}

	public function defaultAction() {
 	
		return new Response();
	}

}
