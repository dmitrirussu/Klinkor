<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 22:37
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\DML\Clauses;



use OmlManager\ORM\Models\Reader;
use OmlManager\ORM\SDB\SDBManagerConnections;

class InsertClause implements DMLClauseInterface {
	const TABLE_NAME = '[TABLE_NAME]';
	const FIELDS = '[FIELDS]';
	const FIELDS_VALUES = '[FIELDS_VALUES]';

	private $_INSERT = 'INSERT INTO [TABLE_NAME] ([FIELDS]) VALUES [FIELDS_VALUES]';

	private $models;

	public function model($object, $alias = null) {
		$this->models[] = $object;

		return $this;
	}

	/**
	 * Models Have to be of the same DataBase
	 * @param $objects
	 * @return $this
	 */
	public function models($objects) {
		$this->models = $objects;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function flush() {

		if ( $this->models ) {

			$statements = array();
			$dataBaseName = null;
			$dataBaseConfName = null;
			$tableName = null;
			$sqlStatement = null;
			$fields = null;
			$key = 1;

			foreach($this->models AS $model) {
				$modelReader = new Reader($model);
				$dataBaseName = $modelReader->getModelDataBaseName();
				$dataBaseConfName = $modelReader->getModelDataDriverConfName();
				$tableName = $modelReader->getModelTableName();
				$properties = $modelReader->getModelProperties();

				$statement = array();
				$fields = array_map(function($field) use ($properties, $modelReader, &$statement, $key) {

					$statement[':'.$field->name.'_'.$key] = $modelReader->getValueByFieldName($field->name);

					return $field->name;

				}, $properties);
				$statements = array_merge($statements, $statement);
				$sqlStatement[] = '('.implode(', ', array_keys($statement)).')';

				$key++;
			}

			$this->_INSERT = str_replace(array(self::TABLE_NAME, self::FIELDS, self::FIELDS_VALUES),
				array($dataBaseName.'.'.$tableName, implode(', ', $fields), implode(', ', $sqlStatement)), $this->_INSERT);

			$result = SDBManagerConnections::getManager($dataBaseConfName)
				->getDriver()->execute($this->_INSERT, $statements);

			if ( empty($result) ) {

				return false;
			}
		}

		return true;
	}
}