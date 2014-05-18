<?php
/**
 * Created by Dumitru Russu.
 * Date: 01.05.2014
 * Time: 15:47
 * OmlManager\ORM\Console${NAME}
 */

namespace OmlManager\ORM\SchemaEntitiesGenerator;

use OmlManager\ORM\Drivers\DriversConfig;
use OmlManager\ORM\Drivers\DriverManagerConnection;


abstract class Generator {

	public $dataBaseName;
	public $schemaName;
	public $realPath;
	public $schemaNameSpace = '';

	/**
	 * @var DriversConfig
	 */
	private $driverConfig;

	/**
	 * @var \OmlManager\ORM\Drivers\DriverManagerConnection
	 */
	private $driver;

	protected $driverConfName;
	protected $driverName;

	public function __construct($dbConfName, $path = null, $nameSpace = null) {
		$this->driverConfig = new DriversConfig($dbConfName);

		$this->dataBaseName = $this->driverConfig->getDataBaseName();
		$this->schemaName = implode(array_map('ucfirst', explode('_', strtolower($this->dataBaseName))));
		$this->schemaNameSpace = $nameSpace;
		$this->driverConfName = $this->driverConfig->getDriverConfName();
		$this->driverName = $this->driverConfig->getDataBaseDriverName();

		$this->realPath = ($path ? $path : realpath(__DIR__));

		$this->driver = new DriverManagerConnection($this->driverConfig);
	}

	abstract public function generateSchemaEntities();
	abstract protected function createPackageClass();

	abstract public function generateEntity($entityName);
	abstract protected function createModelEntityClass($entityName);
	abstract protected function createStoreEntityClass($entityName);

	/**
	 * Get DataBase Tables
	 * @return mixed
	 */
	protected function getAllEntitiesName() {
		$sql = "SELECT TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = :table_schema";

		$query = $this->driver->getDriver()->query($sql, array(':table_schema' => $this->dataBaseName));
		$result = $query->fetchAll();

		return $result;
	}

	/**
	 * Get Entity Foreign Keys
	 * @param $tableName
	 * @param bool $byReferencedTable
	 * @return mixed
	 */
	protected function getAllForeignKey($tableName, $byReferencedTable = true) {

		$where = 'TABLE_NAME = :referenced_table_name AND REFERENCED_TABLE_NAME IS NOT NULL';

		if ( $byReferencedTable ) {
			$where = 'REFERENCED_TABLE_NAME = :referenced_table_name';
		}

		$sql = "SELECT
					TABLE_NAME,
					CONSTRAINT_NAME,
					COLUMN_NAME,
					REFERENCED_COLUMN_NAME,
					REFERENCED_TABLE_NAME
				FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
				WHERE {$where} AND CONSTRAINT_SCHEMA = :schema_name";

		$query = $this->driver->getDriver()->query($sql,
			array(':referenced_table_name' => $tableName, ':schema_name' => $this->dataBaseName));

		$result = $query->fetchAll();
		return $result;
	}

	/**
	 *
	 * @param $entityName
	 * @param $fieldName
	 * @return mixed
	 */
	protected function getEntityRelation($entityName, $fieldName) {
		$sql = "SELECT COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS
					WHERE
						TABLE_NAME = :table_name AND
						TABLE_SCHEMA = :table_schema AND
						COLUMN_NAME = :column_name";

		$query = $this->driver->getDriver()->query($sql,
			array(':table_name' => $entityName, ':table_schema' => $this->dataBaseName,
			':column_name' =>$fieldName
		));

		$result = $query->fetchOne();
		return $result;
	}

	/**
	 * get Entity Info
	 * @param $tableName
	 * @return mixed
	 */
	protected function getEntityInfo($tableName) {
		$query = $this->driver->getDriver()->query("DESCRIBE {$this->dataBaseName}.$tableName", array());

		return $query->fetchAll();
	}


	protected function createPackageFolder() {

		//create package Of DataBase entities
		if ( !is_dir($this->realPath . '/' . $this->schemaName.'Package/Model/') ) {
			//create Folder of Models
			mkdir($this->realPath . '/' . $this->schemaName.'Package/Model/', 0777, true);
		}

		//create Folder of Storing Models Methods
		if ( !is_dir($this->realPath . '/' . $this->schemaName.'Package/Store/') ) {

			mkdir($this->realPath . '/' . $this->schemaName.'Package/Store/', 0777, true);
		}
	}

	/**
	 * @param $type
	 * @return bool|mixed
	 */
	protected function parseFiledType($type) {
		preg_match('/[a-z]+/', $type, $typeResult);

		return $typeResult ? (string)end($typeResult) : false;
	}

	/**
	 * @param $type
	 * @return bool|mixed
	 */
	protected function parseFieldLength($type) {
		preg_match('/[0-9]+/', $type, $length);

		return $length ? (int)end($length) : false;
	}
}

class GeneratorException extends \Exception {

}