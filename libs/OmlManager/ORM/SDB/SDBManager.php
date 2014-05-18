<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 21:10
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\SDB;

use OmlManager\ORM\Query\DDL\Clauses\ClauseInterface;
use OmlManager\ORM\Query\DML\DMLQueryManagerInterface;
use OmlManager\ORM\Query\DML\DMLQueryManager;
use OmlManager\ORM\Query\DDL\DDLQueryManager;
use OmlManager\ORM\Query\OML\OMLQueryManagerBatchOperation;
use OmlManager\ORM\Query\OML\OMLQueryManagerInterface;
use OmlManager\ORM\Query\OML\OMLQueryManager;


abstract class SDBManager implements SDBManagerInterface {

	/**
	 * @return OMLQueryManagerInterface|OMLQueryManagerBatchOperation
	 */
	public static function oml() {

		return new OMLQueryManager();
	}

	/**
	 * @return DMLQueryManagerInterface
	 */
	public static function dml() {

		return new DMLQueryManager();
	}

	/**
	 * @return ClauseInterface
	 */
	public static function ddl() {

		return new DDLQueryManager();
	}
}