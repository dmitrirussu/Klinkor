<?php


namespace TestPackage\Models;

use TestPackage\Test;
use OmlManager\ORM\OmlORManager;
use OmlManager\ORM\Drivers\DriverTransactionInterface;



/**
 * Class Users
 * @table users
 */
class Users  extends Test {

	/**
	* @var $id {@field = id, @type = int, @auto_increment = true, @primary_key = true, @length = 11}
	*/
	private $id;

	/**
	* @var $name {@field = name, @type = varchar, @length = 11}
	*/
	private $name;

	/**
	* @var $age {@field = age, @type = int, @length = 11}
	*/
	private $age;

	/**
	* @var $sex {@field = sex, @type = enum}
	*/
	private $sex;

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
	 * @return int
	 */
	public function getAge() {

		return (int)$this->age;
	}

	public function setAge($value) {
		$this->age = $value;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSex() {

		return (string)$this->sex;
	}

	public function setSex($value) {
		$this->sex = $value;

		return $this;
	}

	/**
	 * @return \OmlManager\ORM\Query\OML\OMLQueryMangerOperationsInterface|DriverTransactionInterface
	 */
	public static function oml() {

		return OmlORManager::oml()->model(new self);
	}

	/**
	 * @return Houses
	 */
	public function getHousesByUserId() {

		return OmlORManager::oml()->model(new Houses())->fetchOne('user_id', $this->getId());
	}

	/**
	 * @return array
	 */
	public function getPrivsByUserId() {

		return OmlORManager::oml()->model(new Privs())->fetchAll('user_id', $this->getId());
	}

}