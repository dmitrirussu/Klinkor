<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 21:43
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\DDL;

use OmlManager\ORM\Models\PackageReader;
use OmlManager\ORM\Models\Reader;
use OmlManager\ORM\Query\DDL\Clauses\ClauseInterface;
use OmlManager\ORM\Query\DDL\Clauses\DataBase;
use OmlManager\ORM\SDB\SDBManagerConnections;

class DDLQueryManager implements ClauseInterface {

	/**
	 * @param $name
	 * @return DataBase
	 */
	public function database($name) {
		//TODO have to be implemented

		return new DataBase($name);
	}

	/**
	 * Package Transaction
	 * @param $modelPackage
	 * @param bool $isModel
	 * @throws DDLException
	 * @return \OmlManager\ORM\Drivers\DriverTransactionInterface
	 */
	public function package($modelPackage, $isModel = false) {

		if ( !is_object($modelPackage) ) {

			throw new DDLException('Define Model Package');
		}

		if ( $isModel ) {

			$packageReader = new Reader($modelPackage);
		}
		else {

			$packageReader = new PackageReader($modelPackage);
		}

		return SDBManagerConnections::getManager($packageReader->getModelDataDriverConfName())->getDriver();
	}
}

class DDLException extends \Exception {

}