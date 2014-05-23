<?php
/**
 * Created by Dumitru Russu.
 * Date: 23.05.2014
 * Time: 22:09
 * ${NAMESPACE}${NAME} 
 */

class OmlManagerQueryTest extends PHPUnit_Framework_TestCase {


	public function testGetOmlMangerData() {

		$exp = new \OmlManager\ORM\Query\Expression\Expression('1=1');

		$data = \OmlManager\ORM\OmlORManager::dml()
			->select()
			->model(new \DemoApp\Models\LaunchPackage\Model\Pages())
			->expression($exp)
			->fetchOne();

		$this->assertObjectHasAttribute('id_page', $data);
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