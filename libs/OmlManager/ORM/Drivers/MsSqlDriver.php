<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 21:08
 * ${NAMESPACE}${NAME} 
 */
namespace OmlManager\ORM\Drivers;


class MsSqlDriver implements DriverInterface {

	private $driversConfig;

	public function __construct(DriversConfig $config) {

		$this->driversConfig = $config;
	}

	public function connect() {

	}

	/**
	 * @param $sql
	 * @param $preparedStatement
	 * @return DriverInterface
	 */
	public function query($sql, array $preparedStatement)
	{
		// TODO: Implement query() method.
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
