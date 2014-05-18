<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 16.04.2014
 * Time: 18:41
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\SDB;

use OmlManager\ORM\Drivers\DriversConfig;
use OmlManager\ORM\Drivers\DriverManagerConnection;

/**
 * Store Database Manager Connections
 * Class SDBManagerConnections
 * @package OmlManager\ORM
 */
class SDBManagerConnections implements SDBManagerConnectInterface {
	private static $_DRIVERS;

	/**
	 * @param string $dbConfName
	 * @return DriverManagerConnection
	 */
	public static function getManager($dbConfName = 'default') {
		$driverConf = new DriversConfig($dbConfName);

		if ( !isset(self::$_DRIVERS[$driverConf->getDriverConfName()]) ) {

			self::$_DRIVERS[$driverConf->getDriverConfName()] = new DriverManagerConnection($driverConf);
		}

		return self::$_DRIVERS[$driverConf->getDriverConfName()];
	}
} 