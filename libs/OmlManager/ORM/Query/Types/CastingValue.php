<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 23.04.2014
 * Time: 07:38
 * OmlManager\ORM\DMLQuery\Types${NAME}
 */

namespace OmlManager\ORM\Query\Types;

class CastingValue {

	private $type;

	private $value;

	public function __construct($value, $type) {

		$this->value = $value;
		$this->type = trim(strtolower($type));
	}

	/**
	 * return casted value
	 * @return bool|float|int|string|null
	 * @throws ValueTypeException
	 */
	public function getValue() {

		switch($this->type) {
			case ValueTypes::VALUE_TYPE_BIT:
			case ValueTypes::VALUE_TYPE_TINYINT:
			case ValueTypes::VALUE_TYPE_BOOLEAN:
			case ValueTypes::VALUE_TYPE_BOOL: {

				$this->value = (bool)$this->value;

				break;
			}
			case ValueTypes::VALUE_TYPE_SMALLINT:
			case ValueTypes::VALUE_TYPE_MEDIUMINT:
			case ValueTypes::VALUE_TYPE_BIGINT:
			case ValueTypes::VALUE_TYPE_INT: {

				$this->value = (int)$this->value;

				break;
			}
			case ValueTypes::VALUE_TYPE_DECIMAL:
			case ValueTypes::VALUE_TYPE_DOUBLE:
			case ValueTypes::VALUE_TYPE_REAL:
			case ValueTypes::VALUE_TYPE_FLOAT: {

				$this->value = (float)$this->value;

				break;
			}
			case ValueTypes::VALUE_TYPE_NULL: {

				$this->value = (unset)$this->value;

				break;
			}
			case ValueTypes::VALUE_TYPE_EMPTY: {

				$this->value = '';

				break;
			}
			case ValueTypes::VALUE_TYPE_SET:
			case ValueTypes::VALUE_TYPE_ENUM:
			case ValueTypes::VALUE_TYPE_BLOB:
			case ValueTypes::VALUE_TYPE_LONGBLOB:
			case ValueTypes::VALUE_TYPE_MEDIUMTEXT:
			case ValueTypes::VALUE_TYPE_TEXT:
			case ValueTypes::VALUE_TYPE_STRING:
			case ValueTypes::VALUE_TYPE_VARCHAR:
			case ValueTypes::VALUE_TYPE_CHAR: {

				$this->value = (string)$this->value;

				break;
			}
			default: {

				throw new ValueTypeException('Missing type of value in casting');
				break;
			}
		}

		return $this->value;
	}

	/**
	 * Return String
	 * @return string
	 * @throws ValueTypeException
	 */
	public function getCastingDataType() {
		switch($this->type) {
			case ValueTypes::VALUE_TYPE_BIT:
			case ValueTypes::VALUE_TYPE_TINYINT:
			case ValueTypes::VALUE_TYPE_BOOLEAN:
			case ValueTypes::VALUE_TYPE_BOOL: {

				$this->value = '(bool)';

				break;
			}
			case ValueTypes::VALUE_TYPE_SMALLINT:
			case ValueTypes::VALUE_TYPE_MEDIUMINT:
			case ValueTypes::VALUE_TYPE_BIGINT:
			case ValueTypes::VALUE_TYPE_INT: {

				$this->value = '(int)';

				break;
			}
			case ValueTypes::VALUE_TYPE_DECIMAL:
			case ValueTypes::VALUE_TYPE_DOUBLE:
			case ValueTypes::VALUE_TYPE_REAL:
			case ValueTypes::VALUE_TYPE_FLOAT: {

				$this->value = '(float)';

				break;
			}
			case ValueTypes::VALUE_TYPE_NULL: {

				$this->value = '(unset)';

				break;
			}
			case ValueTypes::VALUE_TYPE_EMPTY: {

				$this->value = '(unset)';

				break;
			}
			case ValueTypes::VALUE_TYPE_SET:
			case ValueTypes::VALUE_TYPE_TIMESTAMP:
			case ValueTypes::VALUE_TYPE_DATETIME:
			case ValueTypes::VALUE_TYPE_DATE:
			case ValueTypes::VALUE_TYPE_TIME:
			case ValueTypes::VALUE_TYPE_ENUM:
			case ValueTypes::VALUE_TYPE_BLOB:
			case ValueTypes::VALUE_TYPE_LONGBLOB:
			case ValueTypes::VALUE_TYPE_TEXT:
			case ValueTypes::VALUE_TYPE_MEDIUMTEXT:
			case ValueTypes::VALUE_TYPE_LONGTEXT:
			case ValueTypes::VALUE_TYPE_STRING:
			case ValueTypes::VALUE_TYPE_VARCHAR:
			case ValueTypes::VALUE_TYPE_CHAR: {

				$this->value = '(string)';

				break;
			}
			default: {

			throw new ValueTypeException('Missing type of value in casting type=' . $this->type);
			break;
			}
		}

		return $this->value;
	}

	/**
	 * @return string
	 */
	public function getPHPValueType() {
		preg_match('/[a-z]+/', $this->getCastingDataType(), $dataType);

		return end($dataType);
	}
} 