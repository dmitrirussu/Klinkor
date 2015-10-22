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
			$modelReader = null;

			foreach($this->models AS $model) {

				if ( empty($modelReader) ) {
					$modelReader = new Reader($model);
				}

				$dataBaseName = $modelReader->getModelDataBaseName();
				$dataBaseConfName = $modelReader->getModelDataDriverConfName();
				$tableName = $modelReader->getModelTableName();
				$properties = $modelReader->getModelProperties();

				$statement = array();

				if ( $properties ) {
					foreach($properties AS $field) {
						$value = $modelReader->getValueByFieldName($field->name);
						if ( $value !== null) {

							$statement[':'.$field->name.'_'.$key] = ($value ? $value : '');

							if ( $modelReader->getModelPrimaryKey() === $field->name) {
								$statement[':'.$field->name.'_'.$key] = ($value ? $value : 0);
							}

							$fields[] = "`{$field->name}`";
						}
					}
				}

				$statements = array_merge($statements, $statement);
				$sqlStatement[] = '('.implode(', ', array_keys($statement)).')';

				$key++;
			}

			$this->_INSERT = str_replace(array(self::TABLE_NAME, self::FIELDS, self::FIELDS_VALUES),
				array($dataBaseName.'.'.$tableName, implode(', ', array_filter($fields)), implode(', ', $sqlStatement)), $this->_INSERT);

			$result = SDBManagerConnections::getManager($dataBaseConfName)
				->getDriver()->execute($this->_INSERT, $statements);

			if ( empty($result) ) {

				return false;
			}
			else{
				//Set Last Inserted ID
				$data = SDBManagerConnections::getManager($dataBaseConfName)
					->getDriver()->execute('SELECT LAST_INSERT_ID()', $statements);

				$id = $data->fetch(\PDO::FETCH_NUM);

				if ( isset($id[0]) && !empty($id[0])) {
					$lastInsertedId = $id[0];
					$modelReader->setModelPrimaryKeyValue($lastInsertedId);
				}
			}
		}

		return true;
	}
}