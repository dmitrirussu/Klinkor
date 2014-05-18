<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 28.04.2014
 * Time: 16:38
 * OmlManager\ORM\Query\DDL\Clauses${NAME}
 */

namespace OmlManager\ORM\Query\DDL\Clauses;


use OmlManager\ORM\Drivers\DriverTransactionInterface;

interface ClauseInterface {

	/**
	 * @param $name
	 * @return DataBase
	 */
	public function database($name);

	/**
	 * @param $modelPackage
	 * @return DriverTransactionInterface
	 */
	public function package($modelPackage);
}

interface DataBaseClauseInterface {

	/**
	 * @param $name
	 * @return Table
	 */
	public function table($name);
}

interface AlterClauseInterface {
	public function changeAttribute($attributeName);
	public function removeAttribute();
}

interface DropClauseInterface {
	public function dropTable();
	public function dropDatabase();
}

interface CleanClauseInterface {
	public function cleanTable();
}