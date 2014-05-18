<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 21:09
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Drivers;

use OmlManager\ORM\Drivers\Exceptions\DriverException;

class DriverManagerConnection {

	/**
	 * @var DriverInterface
	 */
	private $driver;

	public function __construct(DriverConfigInterface $config) {

		switch($config->getDataBaseDriverName()) {
			case DriversConfig::DRIVER_PDO_MYSQL: {

				$this->driver = new PDODriver($config, PDODriver::DRIVER_MYSQL);
				break;
			}
			case DriversConfig::DRIVER_PDO_ORACLE: {

				$this->driver = new PDODriver($config, PDODriver::DRIVER_ORACLE);
				break;
			}
			case DriversConfig::DRIVER_PDO_MSSQL: {

				$this->driver = new PDODriver($config, PDODriver::DRIVER_MSSQL);
				break;
			}
			case DriversConfig::DRIVER_ORACLE: {

				$this->driver = new OracleDriver($config);
				break;
			}
			case DriversConfig::DRIVER_MSSQL: {

				$this->driver = new MsSqlDriver($config);
				break;
			}

			default: {
				throw new DriverException('Missing driver type');
			}
		}

		$this->getDriver()->connect();
	}

	/**
	 * @return DriverInterface|DriverTransactionInterface
	 */
	public function getDriver() {
		return $this->driver;
	}
}
