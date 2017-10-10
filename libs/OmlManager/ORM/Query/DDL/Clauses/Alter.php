<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 28.04.2014
 * Time: 16:34
 * OmlManager\ORM\Query\DDL\Clauses${NAME}
 */

namespace OmlManager\ORM\Query\DDL\Clauses;


use OmlManager\ORM\Drivers\DriverInterface;

class Alter implements DataBaseClauseInterface {

	private $driver;

	public function __construct($dbName, DriverInterface $driver) {

		$this->driver = $driver;
	}

	/**
	 * @param $name
	 * @return Table
	 */
	public function table($name) {

		return new Table($name, $this->driver);
	}
} 