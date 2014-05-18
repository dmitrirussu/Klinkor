<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 21:06
 * ${NAMESPACE}${NAME} 
 */
namespace OmlManager\ORM\Drivers;


class OracleDriver implements DriverInterface {

	private $driversConfig;

	public function __construct(DriversConfig $config) {

		$this->driversConfig = $config;
	}

	public function connect() {

	}

	public function query($sql) {

	}

	public function autocommit() {

	}

	public function beginTransaction() {

	}

	public function commitTransaction() {

	}

	public function rollbackTransaction() {

	}

	public function fetchAll($object = 'stdClass')
	{
		// TODO: Implement fetchAll() method.
	}

	public function fetchOne($object = 'stdClass')
	{
		// TODO: Implement fetchOne() method.
	}

	public function execute($query, array $prepare)
	{
		// TODO: Implement execute() method.
	}

	public function fetchFields()
	{
		// TODO: Implement fetchFields() method.
	}

	public function getDataTypes()
	{
		// TODO: Implement getDataTypes() method.
	}

	public function getConnection()
	{
		// TODO: Implement getConnection() method.
	}
}