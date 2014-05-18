<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 22:38
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\DML\Clauses;

use OmlManager\ORM\Models\Reader;
use OmlManager\ORM\Query\Expression\Expression;
use OmlManager\ORM\Query\Expression\ExpressionInterface;
use OmlManager\ORM\SDB\SDBManagerConnections;

class UpdateClause implements DMLClauseInterface, DMLUpdateClauseInterface {

	const TABLE_NAME = '[TABLE_NAME]';
	const FIELD_AND_VALUES = '[FIELDS_AND_VALUES]';
	const STATEMENT = '[STATEMENT]';

	private $_UPDATE = 'UPDATE [TABLE_NAME] SET [FIELDS_AND_VALUES] WHERE [STATEMENT]';
	private $_STATEMENT = '';

	private $models = array();
	private $modelsReader = array();

	/**
	 * @var Expression
	 */
	private $expressionObject;

	public function model($object, $alias = null) {
		$this->models[] = $object;
		$this->modelsReader[] = new Reader($object);

		return $this;
	}

	public function models(array $models) {

		$this->models = $models;
		$this->modelsReader = array_map(function($model) use ($models) {
			return new Reader($model);
		}, $models);

		return $this;
	}

	public function expression(ExpressionInterface $exp) {
		$this->expressionObject = $exp;
		$this->expressionObject->checkValuesTypeByModels($this->modelsReader);

		$this->_STATEMENT = $exp->getExpression();

		return $this;
	}

	public function flush() {

		if ( $this->models ) {


			foreach($this->models AS $model) {
				/**
				 * @var $modelReader Reader
				 */
				$modelReader = new Reader($model);

				$tableName = $modelReader->getModelDataBaseName().'.'.$modelReader->getModelTableName();

				$fields = $modelReader->getModelPropertiesTokens();
				$statements = array();

				$fieldValues = array_map(function($field) use ($fields, $modelReader, &$statements) {

					if ( !isset($field['primary_key']) || empty($this->expressionObject)) {

						$propertyValue = $modelReader->getValueByFieldName($field['field']);
						$statements[':'.$field['field']] = $propertyValue;

						return $field['field'] . '= :'.$field['field'];
					}

					return false;

				}, $fields);


				if ( $this->expressionObject ) {
					$statements = array_merge($statements, $this->expressionObject->getPreparedStatement());

					$this->_UPDATE = str_replace(array(self::TABLE_NAME, self::FIELD_AND_VALUES, self::STATEMENT),
						array($tableName, implode(', ', array_filter($fieldValues)), $this->_STATEMENT), $this->_UPDATE);
				}
				else {

					$this->_UPDATE = str_replace(array(self::TABLE_NAME, self::FIELD_AND_VALUES, self::STATEMENT),
						array($tableName, implode(', ', $fieldValues), $modelReader->getModelPrimaryKey().'= :'.$modelReader->getModelPrimaryKey()), $this->_UPDATE);
				}

				$result = SDBManagerConnections::getManager($modelReader->getModelDataDriverConfName())->getDriver()->execute($this->_UPDATE, $statements);

				if ( empty($result) ) {

					return false;
				}
			}
		}

		return true;
	}
}
