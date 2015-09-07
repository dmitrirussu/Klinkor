<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 21.04.2014
 * Time: 22:12
 * OmlManager\ORM\OMLQuery${NAME}
 */

namespace OmlManager\ORM\Query\OML;

use OmlManager\ORM\Drivers\DriverTransactionInterface;
use OmlManager\ORM\Query\DML\Clauses\ExpressionClauseInterface;
use OmlManager\ORM\Query\Expression\Expression;
use OmlManager\ORM\Query\Expression\ExpressionInterface;


interface OMLQueryManagerInterface {
	/**
	 * @param $model
	 * @return OMLQueryMangerOperationsInterface|OMLQueryManagerDeleteOperation|DriverTransactionInterface
	 */
	public function model($model);
}

interface OMLQueryMangerOperationsInterface {

	public function fetchByPk($value, $fetchAssoc = false);
	public function fetchOne($fieldName, $value, $operator = '=', array $orderBy = array(), $fetchAssoc = false);
	public function fetchAll($fieldName, $value, $operator = '=', array $limit = array(), array $orderBy = array(), $fetchAssoc = false);
	public function fetchAllBy(ExpressionInterface $exp, array $limit = array(), array $orderBy = array(), $fetchAssoc = false);
	public function fetchOneBy(ExpressionInterface $exp, array $orderBy = array(), $fetchAssoc = false);
	public function fetch(array $limit = array(), array $orderBy = array(), $fetchAssoc = false);
	public function flush();
}

interface OMLQueryManagerDeleteOperation {
	public function delete();
	public function deleteBy(ExpressionInterface $exp);
	public function deleteByField($fieldName, $value, $operator = '=');
}

interface OMLQueryManagerBatchOperation {
	public function deleteBatch(array $models);
	public function flushBatch(array $batch);
}