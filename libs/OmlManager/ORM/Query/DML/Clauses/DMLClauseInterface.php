<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 22:43
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\DML\Clauses;

use OmlManager\ORM\Query\Expression\ExpressionInterface;

interface DMLClauseInterface {
	/**
	 * @param $object
	 * @param null $alias
	 * @return ExpressionClauseInterface|ClauseSelectInterface
	 */
	public function model($object, $alias = null);
}

interface ExpressionClauseInterface {
	/**
	 * @param ExpressionInterface $object
	 * @return DMLClauseInterface|ClauseSelectInterface
	 */
	public function expression(ExpressionInterface $object);
}
