<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 28.04.2014
 * Time: 18:50
 * OmlManager\ORM\Query\DDL\Clauses${NAME}
 */

namespace OmlManager\ORM\Query\DDL\Clauses;


use OmlManager\ORM\SDB\SDBManagerConnections;

class DataBase {

	private $name;
	private $driver;

	public function __construct($name) {

		$this->name = $name;
		$this->driver = SDBManagerConnections::getManager()->getDriver();
	}

	public function create() {
		//TODO have to be implemented
		return new Create($this->name, $this->driver);
	}

	public function alter() {
		//TODO have to be implemented
		return new Alter($this->name, $this->driver);
	}

	public function clean() {
		//TODO have to be implemented
		return new Clean($this->name, $this->driver);
	}

	public function drop() {
		//TODO have to be implemented
		return new Drop($this->name, $this->driver);
	}
} 