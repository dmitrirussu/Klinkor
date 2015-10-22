<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 16.04.2014
 * Time: 18:54
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\Expression;

use OmlManager\ORM\Models\Reader;
use OmlManager\ORM\Query\Types\ValueTypeException;
use OmlManager\ORM\Query\Types\ValueTypeValidator;

class Expression extends ExpressionInterface {

	private static $_GROUPED = ' (EXP) ';

	private static $_IS = ' IS ';
	private static $_NOT = ' NOT ';
	private static $_IN = ' IN (VALUES) ';
	private static $_LIKE = ' LIKE ';

	private static $_EQUAL = ' = ';
	private static $_LESS = ' < ';
	private static $_GREATER = ' > ';
	private static $_LESS_EQUAL = ' <= ';
	private static $_GREATER_EQUAL = ' >= ';
	private static $_DIFF = ' <> ';

	private static $_AND = ' AND ';
	private static $_OR = ' OR ';

	private $expressions = array();

	private $expressionsDone = array();

	private $expression = null;

	private $finalExpression = null;

	private $isGrouped = false;

	private $fieldsValues = array();
	private $fieldName = null;
	private $alias = null;
	private $prepareStatement = array();
	private $inFieldSValues = array();

	public function __construct($expression = null) {
		$this->expression = null;
		if ( $expression === '1=1') {

			$this->expressions[] = $expression;
		}
	}

	/**
	 * @param array $models
	 * @throws ExpressionException
	 */
	public function checkValuesTypeByModels(array $models) {

		if ( empty($models) ) {

			throw new ExpressionException('Missing DataBase model');
		}

		$this->finalExpression = $this->getExpressionsDone();

		if ( isset($models[0]) ) {
			$models = end($models);

			/**
			 * @var $models Reader
			 */
			$models = $models->getModelPropertiesTokens();
		}


		foreach($models AS $alias => $modelFields) {
			$fieldMacros = null;

			/**
			 * @var $modelFields Reader
			 */

			if ( is_string($alias) ) {
				$modelFields = $modelFields->getModelPropertiesTokens();

				foreach($modelFields as $property) {

					if ( !isset($this->fieldsValues[$alias]) || !array_key_exists($property['field'], $this->fieldsValues[$alias]) ) {
						continue;
					}

					$type = $property['type'];
					$value = $this->fieldsValues[$alias][$property['field']];

					$valueType = new ValueTypeValidator($value, $type, $property['field']);

					if ( is_array($valueType->getValue()) ) {

						$i = 1;
						foreach($valueType->getValue() AS $v ) {

							$this->prepareStatement[':'.$alias.$property['field'].'_'.$i] = $v;

							$i++;
						}
					}
					else {

						$this->prepareStatement[':'.$alias.$property['field']] = $valueType->getValue();
					}
				}
			}
			else {
				if ( !array_key_exists($modelFields['field'], $this->fieldsValues) ) {
					continue;
				}

				$type = $modelFields['type'];
				$value = $this->fieldsValues[$modelFields['field']];

				$valueType = new ValueTypeValidator($value, $type, $modelFields['field']);

				$fieldMacros = ':'.$modelFields['field'];
				$values = $valueType->getValue();
				if ( is_array($values) ) {

					$i = 1;
					foreach($values AS $v) {

						$this->prepareStatement[$fieldMacros.'_'.$i] = $v;
						$i++;
					}
				}
				else {

					$this->prepareStatement[$fieldMacros] = $valueType->getValue();
				}
			}
		}
	}

	public function getPreparedStatement() {

		return $this->prepareStatement;
	}

	public function realEscapeString($value) {
		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
		$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

		return str_replace($search, $replace, $value);
	}

	/**
	 * Put Expression inside of round square
	 * @return $this
	 */
	public function grouped() {

		if ( $this->expressions && empty($this->expression) ) {

			$tmp = array();
			if ( $this->isGrouped ) {

				$this->expressionsDone[] = str_replace('EXP', implode('', $this->expressions), self::$_GROUPED);
				$this->expressions = array();
			}
			else {

				$tmp[] = str_replace('EXP', implode('', $this->expressions), self::$_GROUPED);
				$this->expressions = $tmp;
			}
		}
		else {

			$this->expressions[] = str_replace('EXP', $this->expression, self::$_GROUPED);
		}

		$this->expression = null;
		$this->isGrouped = true;

		return $this;
	}

	/**
	 * Put Expression inside of round square, with next string (YOUR EXPRESSION) AND
	 * @return $this
	 */
	public function groupedAnd() {

		$this->isGrouped = false;

		if ( $this->expressions and empty($this->expression)) {
			$tmp = array();
			$this->expressionsDone[] = str_replace('EXP', implode('', $this->expressions), self::$_GROUPED) . self::$_AND;
			$this->expressions = $tmp;
		}
		else {

			$this->expressions[] = str_replace('EXP', $this->expression, self::$_GROUPED) . self::$_AND;
		}

		$this->expression = null;

		return $this;
	}

	/**
	 * Put Expression inside of round square, with next string (YOUR EXPRESSION) AND
	 * @return $this
	 */
	public function groupedOr() {

		$this->isGrouped = false;

		if ( $this->expressions and empty($this->expression)) {
			$tmp = array();
			$this->expressionsDone[] = str_replace('EXP', implode('', $this->expressions), self::$_GROUPED) . self::$_OR;
			$this->expressions = $tmp;
		}
		else {

			$this->expressions[] = str_replace('EXP', $this->expression, self::$_GROUPED) . self::$_OR;
		}

		$this->expression = null;

		return $this;
	}

	/**
	 * Set Field Name
	 * @param $fieldName
	 * @return $this
	 */
	public function field($fieldName) {

		$fieldArray = explode('.', $fieldName);
		$this->fieldName = (isset($fieldArray[1]) ? $fieldArray[1] : $fieldArray[0]);

		if ( isset($fieldArray[1]) ) {

			$this->alias = $fieldArray[0];
			$this->fieldName = $fieldArray[1];

			$this->fieldsValues[$this->alias][$this->fieldName] = null;
		}
		else {
			$this->fieldsValues[$this->fieldName] = null;
		}


		$this->expression .= $fieldName;

		return $this;
	}

	/**
	 * NOT Equal, NOT LIKE
	 * @return $this
	 */
	public function not() {

		$this->expression .= self::$_NOT;

		return $this;
	}

	/**
	 * Different <>, !=
	 * @param $value
	 * @return $this
	 */
	public function diff($value) {

		$this->expression .= self::$_DIFF . ':'. $this->alias.$this->fieldName;

		$this->setFieldValue($value);

		return $this;
	}

	/**
	 * IN (Array)
	 * @param array $values
	 * @return $this
	 */
	public function in(array $values) {

		$this->inFieldSValues = array();

		if ( count($values) ) {
			$i = 1;

			foreach($values AS $value) {

				$this->inFieldSValues[':'.$this->alias.$this->fieldName.'_'.$i] = $value;
				$i++;
			}
		}

		$this->expression .= str_replace('VALUES', implode(', ', array_keys($this->inFieldSValues)), self::$_IN);

		$this->setFieldValue($this->inFieldSValues);

		return $this;
	}

	/**
	 * LIKE Value
	 * @param $value
	 * @return $this
	 */
	public function like($value) {

		$this->expression .= self::$_LIKE . ':'. $this->alias.$this->fieldName;

		$this->setFieldValue($value);

		return $this;
	}

	/**
	 * IS EQUAL, =
	 * @param $value
	 * @return $this
	 */
	public function equal($value) {

		$this->expression .= self::$_EQUAL . ':'. $this->alias.$this->fieldName;

		$this->setFieldValue($value);

		return $this;
	}

	public function is($value) {

		if ( !is_null($value) ) {

			throw new ValueTypeException('Value of IS statement have to be NULL');
		}

		$this->expression .= self::$_IS . ':'. $this->alias.$this->fieldName;

		$this->setFieldValue($value);

		return $this;
	}

	/**
	 * IS LESS <
	 * @param $value
	 * @return $this
	 */
	public function less($value) {

		$this->expression .= self::$_LESS . ':'. $this->alias.$this->fieldName;

		$this->setFieldValue($value);

		return $this;
	}

	/**
	 * Less and Equal <=
	 * @param $value
	 * @return $this
	 */
	public function lessEqual($value) {

		$this->expression .= self::$_LESS_EQUAL . ':'. $this->alias.$this->fieldName;

		$this->setFieldValue($value);

		return $this;
	}

	/**
	 * IS Greater >
	 * @param $value
	 * @return $this
	 */
	public function greater($value) {

		$this->expression .= self::$_GREATER . ':'. $this->alias.$this->fieldName;

		$this->setFieldValue($value);

		return $this;
	}

	/**
	 * IS Greater And equal >=
	 * @param $value
	 * @return $this
	 */
	public function greaterEqual($value) {

		$this->expression .= self::$_GREATER_EQUAL . ':'. $this->alias.$this->fieldName;

		$this->setFieldValue($value);

		return $this;
	}

	/**
	 * Logic operator AND
	 * @return $this
	 */
	public function andExp() {

		$this->expression .= self::$_AND;

		return $this;
	}

	/**
	 * Logic operator OR
	 * @return $this
	 */
	public function orExp() {

		$this->expression .= self::$_OR;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getExpressionsDone() {
		if ( !empty($this->expressions) ) {
			$this->expressions[] = $this->expression;
			//set default expression
			$expression = implode($this->expressions);
		}
		else {
			//set simple wrote expression
			$expression = $this->expression;
		}

		return $expression;
	}


	/*
	 * return Expression
	 * @return string
	 */
	public function getExpression() {

		return $this->finalExpression;
	}

	/**
	 * Object Expression to string
	 * @return string
	 */
	public function __toString() {

		return $this->getExpression();
	}

	/**
	 * Set Filed Value
	 * @param $value
	 */
	private function setFieldValue($value) {

		if ( isset($this->alias) ) {

			$this->fieldsValues[$this->alias][$this->fieldName] = $value;
		}
		else {
			$this->fieldsValues[$this->fieldName] = $value;
		}

		$this->alias = null;
		$this->fieldName = null;
	}

	/**
	 * @param $value
	 * @param $operator
	 * @return $this
	 * @throws ExpressionException
	 */
	public function operation($value, $operator) {

		if ( empty($operator) ) {

			throw new ExpressionException('Expression Comparison Operator cannot be empty!');
		}

		$operator = strtolower($operator);

		switch($operator) {
			case '=': {
				$this->equal($value);

				break;
			}
			case '<=': {
				$this->lessEqual($value);

				break;
			}
			case '>=': {
				$this->greaterEqual($value);

				break;
			}
			case '>': {
				$this->greater($value);

				break;
			}
			case '<': {
				$this->less($value);

				break;
			}
			case '<>':
			case '!=': {
				$this->diff($value);

				break;
			}
			case 'in': {
				$this->in($value);

				break;
			}
			case 'like': {
				$this->like($value);

				break;
			}

			default: {
				if ( empty($operator) ) {

					throw new ExpressionException('Operator is missing!');
				}
				break;
			}
		}

		return $this;
	}
}

class ExpressionException extends \Exception {

}