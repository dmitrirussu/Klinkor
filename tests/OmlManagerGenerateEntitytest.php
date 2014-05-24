<?php
/**
 * Created by Dumitru Russu.
 * Date: 24.05.2014
 * Time: 18:16
 * ${NAMESPACE}${NAME} 
 */

class OmlManagerGenerateEntityTest extends PHPUnit_Framework_TestCase {

	/**
	 * Generate Db Entity
	 */
	public function testGenerateDbEntity() {

		$result = (bool)system('php console/generator.php create:app:db:entity demo launch bugs');

		echo "Generate DB entity\n";

		$this->assertTrue($result);
	}
} 