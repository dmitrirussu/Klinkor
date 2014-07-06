<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 16.04.2014
 * Time: 18:54
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\Expression;

abstract class ExpressionInterface {

	abstract public function field($fieldName);

	abstract public function not();
	abstract public function like($value);
	abstract public function in(array $values);
	abstract public function is($value);
	abstract public function equal($value);
	abstract public function less($value);
	abstract public function lessEqual($value);
	abstract public function greater($value);
	abstract public function greaterEqual($value);
	abstract public function diff($value);
	abstract public function operation($value, $operator);

	abstract public function grouped();
	abstract public function groupedAnd();
	abstract public function groupedOr();
	abstract public function andExp();
	abstract public function orExp();

	abstract public function getExpression();
	abstract public function checkValuesTypeByModels(array $array);
} 