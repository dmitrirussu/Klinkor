<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 01.05.2014
 * Time: 15:45
 * OmlManager\ORM\Console${NAME}
 */

namespace OmlManager\ORM\SchemaEntitiesGenerator;

use OmlManager\ORM\Query\Types\CastingValue;

class EntityGenerator extends Generator {

	public function generateSchemaEntities() {

		$this->createPackageClass();
	}

	public function generateEntity($entityName) {

		$this->createPackageClass();
		$this->createModelEntityClass($entityName);
		$this->createStoreEntityClass($entityName);
	}

	/**
	 * @throws GeneratorException
	 */
	protected function createPackageClass() {

		//create Database Package Folder
		$this->createPackageFolder();
		$packageFileName = $this->realPath . '/' . $this->schemaName.'Package/'.$this->schemaName.'.php';

		if ( file_exists($packageFileName) ) {

			return true;
		}

		$f = fopen($packageFileName, 'w' );

		if ( empty($f) ) {

			throw new GeneratorException('Was not possible to create Package Class File');
		}

		$class = GeneratorConf::$_NAMESPACE . $this->schemaName.'Package;';

		$class .= str_replace(
			array(GeneratorConf::CLASS_NAME, GeneratorConf::PACKAGE_NAME, GeneratorConf::DATABASE_NAME, GeneratorConf::DRIVER_CONF, GeneratorConf::DRIVER_NAME),
			array($this->schemaName, $this->schemaName.'Package', $this->dataBaseName, $this->driverConfName, $this->driverName),
			GeneratorConf::$_CLASS_PACKAGE_DOC_COMMENT);

		$class .= str_replace(array(GeneratorConf::CLASS_NAME, GeneratorConf::CLASS_EXTENDS, GeneratorConf::CONTENT),
			array($this->schemaName, '', ''), GeneratorConf::$_CLASS);

		fwrite($f, GeneratorConf::$_PHP . $class);

		$result = fclose($f);

		if ( empty($result) ) {

			throw new GeneratorException('Was not possible to Write Package Class File');
		}

		return true;
	}

	/**
	 * @param $entityName
	 * @return bool
	 */
	protected function createModelEntityClass($entityName) {

		$className = implode(array_map('ucfirst', explode('_', strtolower($entityName))));
		$schemaName = implode(array_map('ucfirst', explode('_', strtolower($this->dataBaseName))));

		$f = fopen($this->realPath . '/' . $schemaName.'Package/Model/' . $className.'.php', 'w');

		//entity package namespace
		$namespace = GeneratorConf::$_NAMESPACE . $schemaName . "Package\\Model;\n";

		//namespace use package
		$namespace .= GeneratorConf::$_USE . $schemaName ."Package\\{$schemaName};";
		$namespace .= GeneratorConf::$_USE . "OmlManager\\ORM\\OmlORManager;";
		$namespace .= GeneratorConf::$_USE . "OmlManager\\ORM\\Drivers\\DriverTransactionInterface;
";

		//Entity comment
		$class = $namespace . str_replace(array(GeneratorConf::CLASS_NAME, GeneratorConf::TABLE_NAME),
				array($className, $entityName), GeneratorConf::$_CLASS_DOC_COMMENT);

		//Entity Class Name
		$class .= str_replace(
			array(GeneratorConf::CLASS_NAME, GeneratorConf::CLASS_EXTENDS),
			array($className, str_replace(GeneratorConf::EXTEND_PACKAGE, $schemaName, GeneratorConf::$_CLASS_EXTENDS)),
			GeneratorConf::$_CLASS);

		//attributes and method Setters And Getters
		$this->addEntityFieldsAndSetGetMethods($entityName, $entityFieldsAndMethods);

		$this->addOMLManager($entityFieldsAndMethods);
		//add Foreign Table relation Get Methods
		$this->addRelationForeignEntitiesGetMethods($entityName, $entityFieldsAndMethods);
		$this->addRelationForeignEntitiesGetMethods($entityName, $entityFieldsAndMethods, false);

		$class = str_replace('[CONTENT]', $entityFieldsAndMethods, $class);

		fwrite($f, GeneratorConf::$_PHP . $class);

		return fclose($f);
	}

	/**
	 * @param $entityName
	 * @return bool
	 */
	protected function createStoreEntityClass($entityName) {

		$className = implode(array_map('ucfirst', explode('_', strtolower($entityName))));
		$schemaName = implode(array_map('ucfirst', explode('_', strtolower($this->dataBaseName))));

		if ( file_exists($this->realPath . '/' . $schemaName.'Package/Store/' . $className.'.php') ) {

			return true;
		}

		$f = fopen($this->realPath . '/' . $schemaName.'Package/Store/' . $className.'.php', 'w');

		//entity package namespace
		$namespace = GeneratorConf::$_NAMESPACE . $schemaName . "Package\\Store;\n";

		//namespace use package
		$namespace .= GeneratorConf::$_USE . $schemaName ."Package\\{$schemaName};";
		$namespace .= GeneratorConf::$_USE . "OmlManager\\ORM\\OmlORManager;";

		//Entity comment
		$class = $namespace . str_replace(array(GeneratorConf::CLASS_NAME, GeneratorConf::TABLE_NAME),
				array($className, $entityName), GeneratorConf::$_CLASS_DOC_COMMENT);

		//Entity Class Name
		$class .= str_replace(
			array(GeneratorConf::CLASS_NAME, GeneratorConf::CLASS_EXTENDS),
			array($className, str_replace(GeneratorConf::EXTEND_PACKAGE, $schemaName, GeneratorConf::$_CLASS_EXTENDS)),
			GeneratorConf::$_CLASS);

		$class = str_replace('[CONTENT]', '', $class);
		fwrite($f, GeneratorConf::$_PHP . $class);
		return fclose($f);
	}

	/**
	 * @param $entityName
	 * @param $string
	 * @return string
	 * @throws GeneratorException
	 */
	private function addEntityFieldsAndSetGetMethods($entityName, &$string) {
		$attributes = null;
		$method = null;
		$entities = $this->getEntityInfo($entityName);

		if ( $entities ) {

			foreach($entities AS $entity) {

				$funcName = implode(array_map('ucfirst', explode('_', strtolower($entity->Field))));

				$fieldType = $this->parseFiledType($entity->Type);

				//class Attributes
				$attributes .= str_replace(array(
						GeneratorConf::FIELD_NAME, GeneratorConf::TYPE, GeneratorConf::AUTO_INCREMENT, GeneratorConf::PRIMARY_KEY, GeneratorConf::LENGTH,
					), array(
						$entity->Field, $fieldType,
						$entity->Extra === 'auto_increment' ? GeneratorConf::$ATTRIBUTE_AUTO_INCREMENT : '',
						$entity->Key === 'PRI' ? GeneratorConf::$ATTRIBUTE_PRIMARY_KEY : '',
						$this->parseFieldLength($entity->Type) ?
							GeneratorConf::$ATTRIBUTE_LENGTH . $this->parseFieldLength($entity->Type) : '',
					),
					GeneratorConf::$_ATTRIBUTE_DOC_COMMENT);

				$attributes .= GeneratorConf::$_PRIVATE_VAR . $entity->Field . ";\n";

				$castingDataType = new CastingValue('', $fieldType);

				//Method Doc Comment

				$method .= str_replace(GeneratorConf::TYPE, $castingDataType->getPHPValueType(), GeneratorConf::$_METHOD_DOC_COMMENT);

				//Class Attributes, method Setter and Getter
				$method .= str_replace(
					array(GeneratorConf::FUNC_NAME, GeneratorConf::CONTENT),
					array($funcName, $castingDataType->getCastingDataType().'$this->'.$entity->Field.';'), GeneratorConf::$_GET_METHOD);

				$method .= str_replace(
					array(GeneratorConf::FUNC_NAME, GeneratorConf::CONTENT),
					array($funcName, '$this->'.$entity->Field.' = $value;'), GeneratorConf::$_SET_METHOD);
			}
		}
		else {

			throw new GeneratorException('DataBase table '. $entityName .' does not exist ');
		}

		return $string .= $attributes.$method;
	}

	/**
	 * @param $entityFieldsAndMethods
	 * @return string
	 */
	public function addOMLManager(&$entityFieldsAndMethods) {

		return $entityFieldsAndMethods .= GeneratorConf::$_OML_MANAGER_METHOD;
	}

	/**
	 * Add Relation Foreign Entities Get Methods
	 * @param $entityName
	 * @param $entityFieldsAndMethods
	 * @param bool $byReferencedTable
	 * @return null|string
	 */
	private function addRelationForeignEntitiesGetMethods($entityName, &$entityFieldsAndMethods, $byReferencedTable = true) {
		$methods = null;
		$foreignKeys = $this->getAllForeignKey($entityName, $byReferencedTable);

		if ( $foreignKeys ) {

			foreach ($foreignKeys AS $foreignKey) {
				$funcName = implode(array_map('ucfirst', explode('_', strtolower($foreignKey->TABLE_NAME))));
				$columnName = implode(array_map('ucfirst', explode('_', strtolower($foreignKey->REFERENCED_COLUMN_NAME))));
				$foreignKeyColumnName = implode(array_map('ucfirst', explode('_', strtolower($foreignKey->COLUMN_NAME))));

				if ( $byReferencedTable ) {

					$fetch = 'fetchAll';
					$type = 'array';

					$entityDescription = $this->getEntityRelation($foreignKey->TABLE_NAME, $foreignKey->COLUMN_NAME);

					if ( $entityDescription->COLUMN_KEY == 'UNI' ) {
						$fetch = 'fetchOne';
						$type = $funcName;
					}

					$methods .= str_replace(GeneratorConf::TYPE, $type, GeneratorConf::$_METHOD_DOC_COMMENT);

					$methods .= str_replace(
						array(GeneratorConf::FUNC_NAME, GeneratorConf::CONTENT),
						array($funcName.'By'.$foreignKeyColumnName,
							"OmlORManager::oml()->model(new {$funcName}())->{$fetch}('{$foreignKey->COLUMN_NAME}', \$this->get{$columnName}());"),
						GeneratorConf::$_GET_METHOD);
				}
				else {

					$refFuncName = implode(array_map('ucfirst', explode('_', strtolower($foreignKey->REFERENCED_TABLE_NAME))));

					$methods .= str_replace(GeneratorConf::TYPE, $refFuncName, GeneratorConf::$_METHOD_DOC_COMMENT);

					$methods .= str_replace(
						array(GeneratorConf::FUNC_NAME, GeneratorConf::CONTENT),
						array($refFuncName.'By'.$foreignKeyColumnName,
							"OmlORManager::oml()->model(new {$refFuncName}())->fetchOne('{$foreignKey->REFERENCED_COLUMN_NAME}', \$this->get{$foreignKeyColumnName}());"),
						GeneratorConf::$_GET_METHOD);
				}
			}
		}

		return $entityFieldsAndMethods .= $methods;
	}
} 