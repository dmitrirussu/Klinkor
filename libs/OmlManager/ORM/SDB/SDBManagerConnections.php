<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 16.04.2014
 * Time: 18:41
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\SDB;

use OmlManager\ORM\Drivers\DriverInterface;
use OmlManager\ORM\Drivers\DriversConfig;
use OmlManager\ORM\Drivers\DriverManagerConnection;

/**
 * Store Database Manager Connections
 * Class SDBManagerConnections
 * @package OmlManager\ORM
 */
class SDBManagerConnections implements SDBManagerConnectInterface {
	private static $_DRIVERS;
	private static $ENV = 'DML';


	/**
	 * @param string $dbConfName
	 * @return DriverManagerConnection
	 */
	public static function getManager($dbConfName = 'default', $switchOnWritePort = 0, $ENV = 'DML') {

		if ( $ENV === 'DDL' ) {
			self::$ENV = $ENV;
		}

		if ( self::$ENV === 'DDL' && self::$ENV !== $ENV ) {
			$switchOnWritePort = 1;
		}

		$driverManagerConnection = self::getDriverManagerConnection($dbConfName, $switchOnWritePort);


		if ( $driverManagerConnection->getDriver()->hasOpenedTransaction() || $ENV === 'DDL') {
			return $driverManagerConnection;
		}
		else {
			$driverManagerConnection = self::getDriverManagerConnection($dbConfName, $switchOnWritePort);
			self::$ENV = $ENV;
		}

		return $driverManagerConnection;
	}

	/**
	 * @param $dbConfName
	 * @param $switchOnWritePort
	 * @return DriverManagerConnection
	 */
	private static function getDriverManagerConnection($dbConfName, $switchOnWritePort) {
		$driverConf = DriversConfig::instance($dbConfName, $switchOnWritePort);
		$driverName = $driverConf->getDriverConfName()."_{$switchOnWritePort}";

		if ( !isset(self::$_DRIVERS[$driverName]) ) {

			self::$_DRIVERS[$driverName] = new DriverManagerConnection($driverConf);
		}

		return self::$_DRIVERS[$driverName];
	}
} 