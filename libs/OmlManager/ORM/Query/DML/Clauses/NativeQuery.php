<?php
/**
 * Created by Dumitru Russu.
 * Date: 16.05.2014
 * Time: 00:14
 * OmlManager\ORM\Query${NAME} 
 */

namespace OmlManager\ORM\Query\DDL\Clauses;


use OmlManager\ORM\Drivers\DriverInterface;
use OmlManager\ORM\Models\Reader;
use OmlManager\ORM\Query\DML\Exceptions\DMLQueryManagerException;
use OmlManager\ORM\SDB\SDBManagerConnections;

class NativeQuery {

	private $model;
	/**
	 * @var Reader
	 */
	private $modelReader;

	/**
	 * @var DriverInterface
	 */
	private $driver;

	public function __construct($sql, $bindParamValues, $object) {
		$this->model($object)->query($sql, $bindParamValues);
	}

	private function model($object) {
		$this->model = $object;

		return $this;
	}

	private function query($sql, array $array = array()) {

		if ( empty($this->model) ) {

			throw new DMLQueryManagerException('Model cannot be Empty');
		}

		$this->modelReader = new Reader($this->model);

		$this->driver = SDBManagerConnections::getManager($this->modelReader->getModelDataDriverConfName())
			->getDriver()->query($sql, $array);

		return $this;
	}

	public function fetchAll() {

		return $this->driver->fetchAll(get_class($this->model));
	}

	public function fetchOne() {

		return $this->driver->fetchOne(get_class($this->model));
	}

	public function fetchAssoc() {

		return $this->driver->fetchAssoc();
	}
} 