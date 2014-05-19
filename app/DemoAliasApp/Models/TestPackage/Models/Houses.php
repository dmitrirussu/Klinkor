<?php


namespace TestPackage\Models;

use TestPackage\Test;
use OmlManager\ORM\OmlORManager;
use OmlManager\ORM\Drivers\DriverTransactionInterface;



/**
 * Class Houses
 * @table houses
 */
class Houses  extends Test {

	/**
	* @var $id {@field = id, @type = int, @auto_increment = true, @primary_key = true, @length = 11}
	*/
	private $id;

	/**
	* @var $user_id {@field = user_id, @type = int, @length = 11}
	*/
	private $user_id;

	/**
	* @var $name {@field = name, @type = varchar, @length = 45}
	*/
	private $name;

	/**
	 * @return int
	 */
	public function getId() {

		return (int)$this->id;
	}

	public function setId($value) {
		$this->id = $value;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getUserId() {

		return (int)$this->user_id;
	}

	public function setUserId($value) {
		$this->user_id = $value;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() {

		return (string)$this->name;
	}

	public function setName($value) {
		$this->name = $value;

		return $this;
	}

	/**
	 * @return \OmlManager\ORM\Query\OML\OMLQueryMangerOperationsInterface|DriverTransactionInterface
	 */
	public static function oml() {

		return OmlORManager::oml()->model(new self);
	}

	/**
	 * @return Users
	 */
	public function getUsersByUserId() {

		return OmlORManager::oml()->model(new Users())->fetchOne('id', $this->getUserId());
	}

}