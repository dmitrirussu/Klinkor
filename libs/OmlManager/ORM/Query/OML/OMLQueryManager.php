<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 21.04.2014
 * Time: 22:10
 * OmlManager\ORM\OMLQuery${NAME}
 */

namespace OmlManager\ORM\Query\OML;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use OmlManager\ORM\Drivers\DriverTransactionInterface;
use OmlManager\ORM\OmlORManager;
use OmlManager\ORM\Models\Reader;
use OmlManager\ORM\Query\Expression\Expression;
use OmlManager\ORM\Query\Expression\ExpressionInterface;
use OmlManager\ORM\Query\OML\Exceptions\OMLQueryManagerExceptions;

class OMLQueryManager implements OMLQueryManagerInterface, OMLQueryMangerOperationsInterface,
									OMLQueryManagerDeleteOperation, OMLQueryManagerBatchOperation, DriverTransactionInterface {

	private $model;
	private $alias;
	public function __construct() {}

	/**
	 * @param $model
	 * @return $this|OMLQueryMangerOperationsInterface|OMLQueryManagerDeleteOperation|DriverTransactionInterface
	 * @throws OMLQueryManagerExceptions
	 */
	public function model($model, $alias = null) {

		if ( !is_object($model) ) {

			throw new OMLQueryManagerExceptions('Model cannot be string!');
		}
		elseif(is_array($model)) {

			throw new OMLQueryManagerExceptions('Model cannot be, array!');
		}

		$this->model = $model;
		$this->alias = $alias;

		return $this;
	}

	/**
	 * @param $id
	 * @return mixed
	 * @throws OMLQueryManagerExceptions
	 */
	public function fetchByPk($id, $fetchAssoc = false) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}
		$reader = new Reader($this->model);

		if (empty($id)) {
			throw new OMLQueryManagerExceptions("Primary Key value cannot be empty table: {$reader->getModelTableName()}, empty key: {$reader->getModelPrimaryKey()}");
		}


		$exp = new Expression();
		$exp->field($reader->getModelPrimaryKey())->equal($id);

		return OmlORManager::dml()->select()->model($this->model, $this->alias)->expression($exp)->fetchOne($fetchAssoc);
	}

	/**
	 * @param $fieldName
	 * @param $value
	 * @param string $operator
	 * @param array $orderBy
	 * @throws Exceptions\OMLQueryManagerExceptions
	 * @return mixed
	 */
	public function fetchOne($fieldName, $value, $operator = '=', array $orderBy = array(), $fetchAssoc = false) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		if (empty($fieldName) || empty($operator)) {

			throw new OMLQueryManagerExceptions('FieldName cannot be empty');
		}

		$exp = new Expression();
		$exp->field($fieldName)->operation($value, $operator);

		$omlManagerRequest = OmlORManager::dml()->select()->model($this->model, $this->alias)->expression($exp);

		if ( $orderBy ) {
			$omlManagerRequest->orderBy($orderBy);
		}

		return $omlManagerRequest->fetchOne($fetchAssoc);
	}

	/**
	 * @param $fieldName
	 * @param $value
	 * @param string $operator
	 * @param array $limit
	 * @param array $orderBy
	 * @throws Exceptions\OMLQueryManagerExceptions
	 * @return mixed
	 */
	public function fetchAll($fieldName, $value, $operator = '=', array $limit = array(), array $orderBy = array(), $fetchAssoc = false) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		if (empty($fieldName) || empty($operator)) {

			throw new OMLQueryManagerExceptions('Field name cannot be empty');
		}

		$exp = new Expression();
		$exp->field($fieldName)->operation($value, $operator);

		$omeletteManager = OmlORManager::dml()->select()->model($this->model, $this->alias)->expression($exp);

		if ( $orderBy ) {
			$omeletteManager->orderBy($orderBy);
		}

		if ( $limit ) {
			$startOffset = (isset($limit[1]) ? $limit[0] : 0);
			$endOffset = (!isset($limit[1]) ? $limit[0] : $limit[1]);

			$omeletteManager->limit($startOffset, $endOffset);
		}

		return $omeletteManager->fetchAll($fetchAssoc);
	}

	/**
	 * @param ExpressionInterface $exp
	 * @param array $orderBy
	 * @throws Exceptions\OMLQueryManagerExceptions
	 * @return mixed
	 */
	public function fetchOneBy(ExpressionInterface $exp, array $orderBy = array(), $fetchAssoc = false) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}
		$omlMangerRequest = OmlORManager::dml()->select()->model($this->model, $this->alias)->expression($exp);
		if ( $orderBy ) {
			$omlMangerRequest->orderBy($orderBy);
		}
		return $omlMangerRequest->fetchOne($fetchAssoc);
	}

	/**
	 * @param ExpressionInterface $exp
	 * @param array $orderBy
	 * @throws Exceptions\OMLQueryManagerExceptions
	 * @return mixed
	 */
	public function getRowCountBy(ExpressionInterface $exp) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}
		$omlMangerRequest = OmlORManager::dml()->select()->model($this->model, $this->alias)->expression($exp);

		return $omlMangerRequest->getRowCount();
	}

	/**
	 * @param ExpressionInterface $exp
	 * @param array $limit
	 * @throws OMLQueryManagerExceptions
	 * @return mixed
	 */
	public function fetchAllBy(ExpressionInterface $exp, array $limit = array(), array $orderBy = array(), $fetchAssoc = false) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		$omeletteManager = OmlORManager::dml()->select()->model($this->model, $this->alias)->expression($exp);

		if ( $orderBy ) {
			$omeletteManager->orderBy($orderBy);
		}

		if ( $limit ) {

			$startOffset = (isset($limit[1]) ? $limit[0] : 0);
			$endOffset = (!isset($limit[1]) ? $limit[0] : $limit[1]);

			$omeletteManager->limit($startOffset, $endOffset);
		}

		return $omeletteManager->fetchAll($fetchAssoc);
	}

	/**
	 * @param ExpressionInterface $exp
	 * @param array $limit
	 * @throws OMLQueryManagerExceptions
	 * @return mixed
	 */
	public function fetchAssocAllBy(ExpressionInterface $exp, array $limit = array(), array $orderBy = array(), $fetchAssoc = false) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		$omeletteManager = OmlORManager::dml()->select()->model($this->model, $this->alias)->expression($exp);

		if ( $orderBy ) {
			$omeletteManager->orderBy($orderBy);
		}

		if ( $limit ) {

			$startOffset = (isset($limit[1]) ? $limit[0] : 0);
			$endOffset = (!isset($limit[1]) ? $limit[0] : $limit[1]);

			$omeletteManager->limit($startOffset, $endOffset);
		}

		return $omeletteManager->fetchAssocAll($fetchAssoc);
	}

	/**
	 * @param array $limit
	 * @param array $orderBy
	 * @throws Exceptions\OMLQueryManagerExceptions
	 * @return mixed
	 */
	public function fetch(array $limit = array(), array $orderBy = array(), $fetchAssoc = false) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		$omeletteManager = OmlORManager::dml()->select()->model($this->model, $this->alias)->expression(new Expression('1=1'));

		if ( $orderBy ) {
			$omeletteManager->orderBy($orderBy);
		}

		if ( $limit ) {
			$startOffset = (isset($limit[1]) ? $limit[0] : 0);
			$endOffset = (!isset($limit[1]) ? $limit[0] : $limit[1]);

			$omeletteManager->limit($startOffset, $endOffset);
		}

		return $omeletteManager->fetchAll($fetchAssoc);
	}

	/**
	 * @param array $limit
	 * @param array $orderBy
	 * @throws Exceptions\OMLQueryManagerExceptions
	 * @return mixed
	 */
	public function getRowCount() {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		$omeletteManager = OmlORManager::dml()->select()->model($this->model, $this->alias)->expression(new Expression('1=1'));

		return $omeletteManager->getRowCount();
	}

	/**
	 * @param array $models
	 * @throws OMLQueryManagerExceptions
	 * @return bool
	 */
	public function deleteBatch(array $models) {

		if ( empty($models) ) {

			throw new OMLQueryManagerExceptions('Param should be Array of Models');
		}

		return OmlORManager::dml()->delete()->models($models)->flush();
	}

	/**
	 * @throws OMLQueryManagerExceptions
	 * @return bool
	 */
	public function delete() {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		return OmlORManager::dml()->delete()->model($this->model, $this->alias)->flush();
	}

	/**
	 * @param ExpressionInterface $exp
	 * @throws OMLQueryManagerExceptions
	 * @return bool
	 */
	public function deleteBy(ExpressionInterface $exp) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		return OmlORManager::dml()->delete()->model($this->model, $this->alias)->expression($exp)->flush();
	}

	/**
	 * @param $fieldName
	 * @param $value
	 * @param string $operator
	 * @throws OMLQueryManagerExceptions
	 * @return bool
	 */
	public function deleteByField($fieldName, $value, $operator = '=') {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		$exp = new Expression();
		$exp->field($fieldName)->operation($value, $operator);

		return OmlORManager::dml()->delete()->model($this->model, $this->alias)->expression($exp)->flush();
	}


	/**
	 * @param Expression $exp
	 * @param array $updateFields
	 *
	 * @return mixed
	 */
	public function updateBy(Expression $exp, array $updateFields) {

		if ( empty($exp) ) {
			throw new InvalidArgumentException('Missing Expression');
		}

		if ( empty($updateFields) ) {
			throw new InvalidArgumentException('Missing updateFields');
		}

		return OmlORManager::dml()->update()->model($this->model, $this->alias)->setFieldsAffect($updateFields)->expression($exp)->flush();
	}


	/**
	 * @param $fieldName
	 * @param $value
	 * @param array $updateFields
	 *
	 * @return mixed
	 */
	public function updateByField($fieldName, $value, array $updateFields) {

		if ( empty($fieldName) ) {
			throw new InvalidArgumentException('Missing fieldName');
		}

		if ( empty($updateFields) ) {
			throw new InvalidArgumentException('Missing updateFields');
		}

		$exp = new Expression();
		$exp->field($fieldName)->equal($value);

		return OmlORManager::dml()->update()->model($this->model, $this->alias)->setFieldsAffect($updateFields)->expression($exp)->flush();
	}


	/**
	 * @throws OMLQueryManagerExceptions
	 * @return bool
	 */
	public function flush($forceInsert = false) {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		$modelReader = new Reader($this->model);

		//insert new Model
		if ( !$modelReader->getModelPrimaryKeyValue() && !$forceInsert) {

			return OmlORManager::dml()->insert()->model($this->model, $this->alias)->flush();
		}

		//update Model
		return OmlORManager::dml()->update()->model($this->model, $this->alias)->flush();
	}

	public function flushBatch(array $models) {

		if ( empty($models) ) {

			throw new OMLQueryManagerExceptions('Param should be array of Models Cannot be empty');
		}


		if ( is_array($models) ) {

			foreach($models AS $model) {
				$modelReader = new Reader($model);
				//insert new Model

				if ( !$modelReader->getModelPrimaryKeyValue() ) {

					$result = OmlORManager::dml()->insert()->model($model, $this->alias)->flush();
				}
				else {
					//update Model
					$result = OmlORManager::dml()->update()->model($model, $this->alias)->flush();
				}

				if ( empty($result) ) {

					return false;
				}
			}
		}

		return true;
	}

	public function beginTransaction() {
		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}


		return OmlORManager::ddl()->package($this->model, true)->beginTransaction();
	}

	public function commitTransaction() {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}

		return OmlORManager::ddl()->package($this->model, true)->commitTransaction();
	}

	public function rollbackTransaction() {

		if ( empty($this->model) ) {

			throw new OMLQueryManagerExceptions('Model Cannot be empty');
		}


		return OmlORManager::ddl()->package($this->model, true)->rollbackTransaction();
	}
}
