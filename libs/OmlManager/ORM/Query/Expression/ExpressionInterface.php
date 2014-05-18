<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 16.04.2014
 * Time: 18:54
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\Expression;

interface ExpressionInterface {

	public function field($fieldName);

	public function not();
	public function like($value);
	public function in(array $values);
	public function is($value);
	public function equal($value);
	public function less($value);
	public function lessEqual($value);
	public function greater($value);
	public function greaterEqual($value);
	public function diff($value);
	public function operation($value, $operator);

	public function grouped();
	public function groupedAnd();
	public function groupedOr();
	public function andExp();
	public function orExp();

	public function getExpression();
	public function checkValuesTypeByModels(array $array);
} 