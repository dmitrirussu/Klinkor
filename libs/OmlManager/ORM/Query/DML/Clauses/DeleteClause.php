<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 22:38
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\DML\Clauses;

use OmlManager\ORM\Models\Reader;
use OmlManager\ORM\Query\DML\Exceptions\DMLQueryManagerException;
use OmlManager\ORM\Query\Expression\Expression;
use OmlManager\ORM\Query\Expression\ExpressionInterface;
use OmlManager\ORM\SDB\SDBManagerConnections;

class DeleteClause implements DMLClauseInterface {

	const TABLE_NAME = '[TABLE_NAME]';

	private $_FROM = 'DELETE FROM [TABLE_NAME]';
	private $_WHERE = ' WHERE ';

	private $models = array();
	private $modelsReader = array();

	/**
	 * @var Expression
	 */
	private $expressionObject;

	/**
	 * @param array $models
	 * @return $this|ExpressionClauseInterface
	 */
	public function models(array $models) {

		$this->models = $models;

		if ( $models ) {
			foreach($models AS $model) {
				$this->modelsReader[] = new Reader($model);
			}
		}

		return $this;
	}

	/**
	 * @param $object
	 * @param null $alias
	 * @return $this|ClauseSelectInterface|ExpressionClauseInterface
	 */
	public function model($object, $alias = null) {

		$this->models[] = $object;
		$this->modelsReader[] = new Reader($object);

		return $this;
	}

	/**
	 * @param ExpressionInterface $object
	 * @return $this
	 * @throws \OmlManager\ORM\Query\DML\Exceptions\DMLQueryManagerException
	 */
	public function expression(ExpressionInterface $object) {

		$this->expressionObject = $object;

		$this->expressionObject->checkValuesTypeByModels($this->modelsReader);

		$this->_WHERE .= $object->getExpression();

		if (count($this->models) > 1) {

			throw new DMLQueryManagerException('Cannot be implemented expression if are more models');
		}


		return $this;
	}

	public function flush() {

		if (empty($this->models)) {

			throw new DMLQueryManagerException('Missing model on delete action');
		}

		foreach($this->models AS $model) {
			$query = null;
			$modelReader = new Reader($model);

			//delete query statement
			$query .= str_replace(self::TABLE_NAME, $modelReader->getModelDataBaseName() .'.'. $modelReader->getModelTableName(), $this->_FROM );

			if ( $this->expressionObject ) {

				$query .= $this->_WHERE;
				$statementParam = $this->expressionObject->getPreparedStatement();
			}
			else {
				$primaryKey = $modelReader->getModelPrimaryKey();
				if ( !$modelReader->getModelPrimaryKeyValue() ) {

					throw new DMLQueryManagerException('Primary key cannot be empty');
				}

				$query .= $this->_WHERE . $modelReader->getModelTableName() .'.'. $primaryKey . ' = ' . ':'.$primaryKey;

				$statementParam = array(':'.$primaryKey => (int)$modelReader->getModelPrimaryKeyValue());
			}

			$driver = SDBManagerConnections::getManager($modelReader->getModelDataDriverConfName());
			$result = $driver->getDriver()->execute($query, $statementParam);

			if ( empty($result) ) {

				return false;
			}
		}

		return true;
	}
} 