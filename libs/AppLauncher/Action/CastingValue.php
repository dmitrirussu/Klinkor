<?php
/**
 * Created by Dumitru Russu.
 * Date: 04.05.2014
 * Time: 15:06
 * Request${NAME} 
 */

namespace AppLauncher\Action;


class CastingValue {
	private $value;
	private $type;

	const TYPE_INT = 'int';
	const TYPE_STRING = 'string';
	const TYPE_CHAR = 'char';
	const TYPE_STR = 'str';
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_NULL = 'null';
	const TYPE_ARRAY = 'array';

	public function __construct($value, $type) {

		$this->value = $value;
		$this->type = $type;
	}

	public function getValue() {
		switch($this->type) {
			case self::TYPE_INT: {

				$this->value = (int)$this->value;

				break;
			}
			case self::TYPE_STR:
			case self::TYPE_CHAR:
			case self::TYPE_STRING: {

				$this->value = (string)$this->value;

				break;
			}
			case self::TYPE_BOOLEAN: {

				$this->value = (bool)$this->value;

				break;
			}
			case self::TYPE_NULL: {

				$this->value = (unset)$this->value;

				break;
			}
			case self::TYPE_ARRAY: {
				if ( !is_array($this->value)) {

					throw new CastingValueException('Casting Value Expected to be array');
				}
				$this->value = (array)$this->value;
				break;
			}
		}

		return $this->value;
	}
}

class CastingValueException extends \Exception {

}