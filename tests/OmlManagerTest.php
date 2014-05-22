<?php
/**
 * Created by Dumitru Russu.
 * Date: 21.05.2014
 * Time: 22:59
 * ${NAMESPACE}${NAME} 
 */

class OmlManagerTest extends PHPUnit_Framework_TestCase {


	/**
	 * Generate Database Entities
	 */
	public function testGenerateDbEntities() {

		$result = (bool)system('php console/generator.php create:app:db:entities demo launch');

		echo "Generate Db Entities\n";

		$this->assertTrue($result);
	}


	/**
	 * Generate Db Entity
	 */
	public function testGenerateDbEntity() {

		$result = (bool)system('php console/generator.php create:app:db:entity demo launch bugs');

		echo "Generate DB entity\n";

		$this->assertTrue($result);
	}

	public function testOmlFetchAll() {

		$pages = \DemoApp\Models\LaunchPackage\Model\Pages::oml()->fetch();

		$this->assertTrue((bool)count($pages));
	}

	public function testOmlInsert() {

		$page = new \DemoApp\Models\LaunchPackage\Model\Pages();

		$page->setPKey('about_us');
		$page->setIdProjectController(10);


		$result = \OmlManager\ORM\OmlORManager::oml()->model($page)->flush();

		$this->assertTrue($result);
	}

} 