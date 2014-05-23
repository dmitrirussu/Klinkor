<?php
/**
 * Created by Dumitru Russu.
 * Date: 22.05.2014
 * Time: 00:14
 * ${NAMESPACE}${NAME} 
 */

class AppLauncherTest extends PHPUnit_Framework_TestCase {


	public function testCreateApp() {

		$result = (bool)system('php console/generator.php create:app demo');

		echo "Generate DemoApp\n";

		$this->assertTrue($result);

	}

	public function testCreateAppController() {

		$result = (bool)system('php console/generator.php create:app:controller Demo About');

		echo "Generate App Controller ->About\n";

		$this->assertTrue($result);

	}

	public function testCreateAppAlias() {

		$result = (bool)system('php console/generator.php create:app:alias DemoAlias Demo');

		echo "Generate DemoApp\n";

		$this->assertTrue($result);

	}

	public function testCreate() {

		$result = (bool)system('php console/generator.php create:app:alias DemoAlias Demo');

		echo "Generate DemoApp\n";

		$this->assertTrue($result);
	}
} 