<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 02.05.2014
 * Time: 22:34
 * OmlManager\ORM\Query\DDL\Clauses${NAME} 
 */

namespace OmlManager\ORM\Query\DDL\Clauses;


use OmlManager\ORM\Drivers\DriverInterface;

class Drop implements DataBaseClauseInterface {

	private $driver;

	public function __construct($dbName, DriverInterface $driver) {

		$this->driver = $driver;
	}


	/**
	 * @param $name
	 * @return DataBase
	 */
	public function table($name) {

		return new Table($name, $this->driver);
	}
} 