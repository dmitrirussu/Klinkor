<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 16.04.2014
 * Time: 18:30
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\SDB;

use OmlManager\ORM\Query\DDL\DDLQueryManagerInterface;
use OmlManager\ORM\Query\DML\DMLQueryManagerInterface;
use OmlManager\ORM\Query\OML\OMLQueryManagerInterface;

interface SDBManagerInterface {

	/**
	 * @return DMLQueryManagerInterface
	 */
	public static function dml();

	/**
	 * @return DDLQueryManagerInterface
	 */
	public static function ddl();

	/**
	 * @return OMLQueryManagerInterface
	 */
	public static function oml();
} 