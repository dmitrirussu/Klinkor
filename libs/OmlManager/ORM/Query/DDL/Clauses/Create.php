<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 28.04.2014
 * Time: 16:34
 * OmlManager\ORM\Query\DDL\Clauses${NAME}
 */

namespace OmlManager\ORM\Query\DDL\Clauses;


use OmlManager\ORM\Drivers\DriverInterface;


class Create implements DataBaseClauseInterface {

	private $_CREATE = 'CREATE ';
	private $_DATABASE = 'DATABASE ';

	private $_NOT_EXIST = 'IF NOT EXISTS ';

	private $driver;

	public function __construct($dbName, DriverInterface $driver) {
		$this->driver = $driver;

		$queryCreateDataBase = $this->_CREATE . $this->_DATABASE . $this->_NOT_EXIST . $dbName;

		$this->driver->execute($queryCreateDataBase, array());
	}

	/**
	 * @param $name
	 * @return Table
	 */
	public function table($name) {

		return new Table($name, $this->driver);
	}
} 