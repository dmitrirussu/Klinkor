<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 22:12
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Drivers;

use OmlManager\ORM\Drivers\Exceptions\DriverConfigException;

class DriversConfig implements DriverConfigInterface {

	const DRIVER_PDO_MYSQL = 'pdo_mysql';

	/**
	 * Use unstable php functions for pdo_oracle, pdo_mssql
	 */
	const DRIVER_PDO_ORACLE = 'pdo_oracle';
	const DRIVER_PDO_MSSQL = 'pdo_mssql';

	/**
	 * Use stable php functions for oracle, mssql
	 */
	const DRIVER_ORACLE = 'oracle';
	const DRIVER_MSSQL = 'mssql';


	private static $DBS_CONNECTIONS;
	private $confName;
	const DATABASE_CONF_FILE_PATH = '/../../../config/databases.ini';


	public function __construct($dbConfigName = 'default') {

		if ( empty($dbConfigName) ) {

			throw new DriverConfigException('Missing database name ' . $this->confName);
		}

		$this->confName = $dbConfigName;

		self::$DBS_CONNECTIONS = new \stdClass();

		//Conf for TEST Database
		self::$DBS_CONNECTIONS->default = new \stdClass();
		self::$DBS_CONNECTIONS->default->driver = self::DRIVER_PDO_MYSQL;
		self::$DBS_CONNECTIONS->default->host = 'localhost';
		self::$DBS_CONNECTIONS->default->db_name = 'test';
		self::$DBS_CONNECTIONS->default->user = 'root';
		self::$DBS_CONNECTIONS->default->password = '';
		self::$DBS_CONNECTIONS->default->port = '';

		if ( file_exists(dirname(__DIR__) . self::DATABASE_CONF_FILE_PATH) ) {

			$databasesConfig = parse_ini_file(dirname(__DIR__) . self::DATABASE_CONF_FILE_PATH, true);

			if ( $databasesConfig ) {
				foreach ($databasesConfig AS $dbName => $database) {
					self::$DBS_CONNECTIONS->{$dbName} = new \stdClass();
					self::$DBS_CONNECTIONS->{$dbName}->driver = (isset($database['driver']) ? $database['driver'] : self::DRIVER_PDO_MYSQL);
					self::$DBS_CONNECTIONS->{$dbName}->host = (isset($database['host']) ? $database['host'] : '');
					self::$DBS_CONNECTIONS->{$dbName}->db_name = (isset($database['db_name']) ? $database['db_name'] : '');
					self::$DBS_CONNECTIONS->{$dbName}->user = (isset($database['user']) ? $database['user'] : '');
					self::$DBS_CONNECTIONS->{$dbName}->password = (isset($database['password']) ? $database['password'] : '');
					self::$DBS_CONNECTIONS->{$dbName}->port = (isset($database['port']) ? $database['port'] : '');
				}
			}
		}
	}

	/**
	 * @return mixed
	 * @throws DriverConfigException
	 */
	public function getConfig() {

		if ( !isset(self::$DBS_CONNECTIONS->{$this->confName}) ) {

			throw new DriverConfigException('Missing database with confName = ' . $this->confName);
		}

		return self::$DBS_CONNECTIONS->{$this->confName};
	}

	/**
	 * Get driver Conf name
	 * @return mixed
	 */
	public function getDriverConfName() {

		return $this->confName;
	}

	/**
	 * Database Name
	 * @throws DriverConfigException
	 * @return string
	 */
	public function getDataBaseName() {

		if ( !isset(self::$DBS_CONNECTIONS->{$this->confName}->{'db_name'}) ) {

			throw new DriverConfigException('Missing db_name, ConfName =' . $this->confName);
		}

		return self::$DBS_CONNECTIONS->{$this->confName}->{'db_name'};
	}

	/**
	 * Driver Name
	 * @throws DriverConfigException
	 * @return string
	 */
	public function getDataBaseDriverName() {

		if ( !isset(self::$DBS_CONNECTIONS->{$this->confName}->{'driver'}) ) {

			throw new DriverConfigException('Missing DB driver, ConfName =' . $this->confName);
		}

		return self::$DBS_CONNECTIONS->{$this->confName}->{'driver'};
	}

	/**
	 * @return mixed
	 * @throws DriverConfigException
	 */
	public function getDataBaseHost() {

		if ( !isset(self::$DBS_CONNECTIONS->{$this->confName}->{'host'}) ) {

			throw new DriverConfigException('Missing hostName database ' . $this->confName);
		}

		return self::$DBS_CONNECTIONS->{$this->confName}->{'host'};
	}

	/**
	 * @return mixed
	 * @throws DriverConfigException
	 */
	public function getDataBaseUser() {
		if ( !isset(self::$DBS_CONNECTIONS->{$this->confName}->{'user'}) ) {

			throw new DriverConfigException('Missing user database ' . $this->confName);
		}

		return self::$DBS_CONNECTIONS->{$this->confName}->{'user'};
	}

	/**
	 * @return mixed
	 * @throws DriverConfigException
	 */
	public function getDataBasePassword() {
		if ( !isset(self::$DBS_CONNECTIONS->{$this->confName}->{'password'}) ) {

			throw new DriverConfigException('Missing password database ' . $this->confName);
		}

		return self::$DBS_CONNECTIONS->{$this->confName}->{'password'};
	}

	/**
	 * @return mixed
	 * @throws DriverConfigException
	 */
	public function getDataBasePort() {
		if ( !isset(self::$DBS_CONNECTIONS->{$this->confName}->{'port'}) ) {

			throw new DriverConfigException('Missing port database ' . $this->confName);
		}

		return self::$DBS_CONNECTIONS->{$this->confName}->{'port'};
	}
}
