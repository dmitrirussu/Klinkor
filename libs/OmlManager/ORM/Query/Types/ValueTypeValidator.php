<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 19.04.2014
 * Time: 16:27
 * ${NAMESPACE}${NAME}
 */

namespace OmlManager\ORM\Query\Types;



class ValueTypeValidator implements ValueTypeInterface {

	private $value;
	private $type;
	private $fieldName;

	private static $_VALUE_TYPES_STRINGS = array(
		ValueTypes::VALUE_TYPE_CHAR,
		ValueTypes::VALUE_TYPE_VARCHAR,
		ValueTypes::VALUE_TYPE_STRING,
		ValueTypes::VALUE_TYPE_MEDIUMTEXT,
		ValueTypes::VALUE_TYPE_TEXT,
	);

	public function __construct($value, $type, $fieldName = null) {
		$this->value = $value;
		$this->type = (is_null($value) && !in_array($type, self::$_VALUE_TYPES_STRINGS) ? ValueTypes::VALUE_TYPE_NULL : $type);
		$this->fieldName = $fieldName;
	}

	public function setValue($value) {

		$this->value = $value;

		return $this;
	}

	public function setType($type = 'char') {

		$this->type = $type;

		return $this;
	}

	public function getValue() {
		$isArray = is_array($this->value);
		$values = $isArray ? $this->value : array($this->value);

		foreach ($values AS &$value) {
			$this->value = $value;

			switch($this->type) {
				case ValueTypes::VALUE_TYPE_BIT:
				case ValueTypes::VALUE_TYPE_BOOLEAN:
				case ValueTypes::VALUE_TYPE_BOOL: {

					if ( !is_bool($this->value) && !is_null($this->value) ) {

						throw new ValueTypeException('Wrong field \''. $this->fieldName .'\' value type, should be boolean');
					}

					$this->value = (int)$this->value;

					break;
				}
				case ValueTypes::VALUE_TYPE_SMALLINT:
				case ValueTypes::VALUE_TYPE_MEDIUMINT:
				case ValueTypes::VALUE_TYPE_BIGINT:
				case ValueTypes::VALUE_TYPE_TINYINT:
				case ValueTypes::VALUE_TYPE_INT: {

					if ( !is_int($this->value) && !is_bool($this->value) && !is_null($this->value) ) {

						throw new ValueTypeException('Wrong field \''. $this->fieldName .'\' value type, should be integer');
					}

					$this->value = (is_null($this->value) ? null : (int)$this->value);

					break;
				}
				case ValueTypes::VALUE_TYPE_DECIMAL:
				case ValueTypes::VALUE_TYPE_DOUBLE:
				case ValueTypes::VALUE_TYPE_REAL:
				case ValueTypes::VALUE_TYPE_FLOAT: {

					if ( !is_float($this->value) && !is_null($this->value) ) {

						throw new ValueTypeException('Wrong field \''. $this->fieldName .'\' value type, should be float');
					}

					$this->value = (is_null($this->value) ? $this->value : (float)$this->value);

					break;
				}
				case ValueTypes::VALUE_TYPE_NULL: {

					if ( !is_null($this->value) && !empty($this->value) ) {
						throw new ValueTypeException('Wrong field \''. $this->fieldName .'\' value type, should be null');
					}

					$this->value = null;

					break;
				}
				case ValueTypes::VALUE_TYPE_EMPTY: {

					if ( !empty($this->value) && !is_null($this->value) ) {

						throw new ValueTypeException('Wrong field \''. $this->fieldName .'\' value type, should be EMPTY');
					}

					$this->value = (is_null($this->value) ? $this->value : '');

					break;
				}
				case ValueTypes::VALUE_TYPE_SET:
				case ValueTypes::VALUE_TYPE_TIMESTAMP:
				case ValueTypes::VALUE_TYPE_DATETIME:
				case ValueTypes::VALUE_TYPE_DATE:
				case ValueTypes::VALUE_TYPE_TIME:
				case ValueTypes::VALUE_TYPE_LONGTEXT:
				case ValueTypes::VALUE_TYPE_MEDIUMTEXT:
				case ValueTypes::VALUE_TYPE_ENUM:
				case ValueTypes::VALUE_TYPE_BLOB:
				case ValueTypes::VALUE_TYPE_TEXT:
				case ValueTypes::VALUE_TYPE_STRING:
				case ValueTypes::VALUE_TYPE_VARCHAR:
				case ValueTypes::VALUE_TYPE_CHAR: {

					if ( !is_string($this->value) && !is_null($this->value) ) {

						throw new ValueTypeException('Wrong field \''. $this->fieldName .'\' value type, should be string');
					}

					$this->value = (is_null($this->value) ? $this->value : trim($this->value));

					break;
				}
				default: {

					throw new ValueTypeException('Missing type of value, or another');

					break;
				}
			}
			$value = $this->value;
		}

		return ($isArray ? $values : $this->value);
	}

	/**
	 * Available Types of Value
	 * @return array
	 */
	public static function getAvailableValueTypes() {
		return array(
			//int
			ValueTypes::VALUE_TYPE_INT,
			ValueTypes::VALUE_TYPE_SMALLINT,
			ValueTypes::VALUE_TYPE_TINYINT,
			ValueTypes::VALUE_TYPE_MEDIUMINT,
			ValueTypes::VALUE_TYPE_BIGINT,
			//float
			ValueTypes::VALUE_TYPE_DECIMAL,
			ValueTypes::VALUE_TYPE_FLOAT,
			ValueTypes::VALUE_TYPE_DOUBLE,
			ValueTypes::VALUE_TYPE_REAL,
			//char
			ValueTypes::VALUE_TYPE_CHAR,
			ValueTypes::VALUE_TYPE_VARCHAR,
			ValueTypes::VALUE_TYPE_STRING,
			ValueTypes::VALUE_TYPE_MEDIUMTEXT,
			ValueTypes::VALUE_TYPE_TEXT,
			ValueTypes::VALUE_TYPE_LONGTEXT,
			//datetime
			ValueTypes::VALUE_TYPE_DATETIME,
			ValueTypes::VALUE_TYPE_TIMESTAMP,
			//boolean
			ValueTypes::VALUE_TYPE_BIT,
			ValueTypes::VALUE_TYPE_BOOL,
			ValueTypes::VALUE_TYPE_BOOLEAN,
			//null
			ValueTypes::VALUE_TYPE_NULL,
			//empty
			ValueTypes::VALUE_TYPE_EMPTY,
			//image
			ValueTypes::VALUE_TYPE_BLOB,
			ValueTypes::VALUE_TYPE_LONGBLOB,

			//strings
			ValueTypes::VALUE_TYPE_ENUM,
			ValueTypes::VALUE_TYPE_SET
		);
	}
}
