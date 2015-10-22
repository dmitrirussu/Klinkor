<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 21:06
 * ${NAMESPACE}${NAME} 
 */
namespace OmlManager\ORM\Drivers;



class PDODriver implements DriverInterface, DriverTransactionInterface {

	const DRIVER_MYSQL = 'mysql';
	const DRIVER_MSSQL = 'mssql';
	const DRIVER_ORACLE = 'oci';

	/**
	 * @var DriversConfig
	 */
	private $config;

	/**
	 * @var \PDO
	 */
	private $driver;
	private $driverName;

	private static $_DATA_TYPES = array(
		1 => 'tinyint',
		2 => 'smallint',
		3 => 'int',
		4 => 'float',
		5 => 'double',
		7 => 'timestamp',
		8 => 'bigint',
		9 => 'mediumint',
		10 => 'date',
		11 => 'time',
		12 => 'datetime',
		13 => 'year',
		16 => 'bit',
		252 => 'string', //BLOB etc image
		253 => 'varchar',
		254 => 'char',
		246 => 'decimal'
	);

	/**
	 * @var \PDOStatement
	 */
	private $queryResult;

	public function __construct(DriverConfigInterface $config, $driverName = self::DRIVER_MYSQL) {

		$this->config = $config;
		$this->driverName = $driverName;
	}

	public function connect() {

		$dbName = ($this->config->getDataBaseName() ? "dbname={$this->config->getDataBaseName()}" : '');
		$this->driver = new \PDO("{$this->driverName}:host={$this->config->getDataBaseHost()};port={$this->config->getDataBasePort()};{$dbName}",
		$this->config->getDataBaseUser(),
		$this->config->getDataBasePassword());

		$this->driver->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	public function getConnection() {
		return $this->driver;
	}

	public function query($sql, array $preparedStatement) {
		$this->execute($sql, $preparedStatement);

		return $this;
	}

	public function fetchAll($object = 'stdClass') {
		if ($object === \PDO::FETCH_ASSOC) {
			return $this->queryResult->fetchAll(\PDO::FETCH_CLASS);
		}

		return (empty($this->queryResult) ? false :$this->queryResult->fetchAll(\PDO::FETCH_CLASS, $object));
	}

	public function fetchOne($object = 'stdClass') {
		if ($object === \PDO::FETCH_ASSOC) {
			return $this->queryResult->fetch(\PDO::FETCH_CLASS);
		}

		return (empty($this->queryResult) ? false : $this->queryResult->fetchObject($object));
	}

	/**
	 * @param $query
	 * @param array $prepare
	 * @return \PDOStatement
	 */
	public function execute($query, array $prepare) {
		$this->queryResult = $this->driver->prepare( $query );
		$this->queryResult->execute( $prepare );

		return $this->queryResult;
	}

	public function fetchFields() {

	}

	public function getDataTypes() {

		return self::$_DATA_TYPES;
	}

	public function beginTransaction() {

		$this->driver->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);

		return $this->driver->beginTransaction();
	}

	public function commitTransaction() {
		$result = $this->driver->commit();

		$this->driver->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);

		return $result;
	}

	public function rollbackTransaction() {
		$result = $this->driver->rollBack();

		$this->driver->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);

		return $result;
	}

	public function __destruct() {

		unset($this->driver);
	}
} 