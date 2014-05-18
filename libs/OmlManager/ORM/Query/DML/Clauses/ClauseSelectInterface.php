<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 26.04.2014
 * Time: 14:13
 * OmlManager\ORM\Query\DML\Clauses${NAME}
 */

namespace OmlManager\ORM\Query\DML\Clauses;


interface ClauseSelectInterface {
	public function fetchOne();
	public function fetchAll();

	/**
	 * @param $joinPrimaryForeignKey
	 * @return DMLClauseInterface
	 */
	public function join($joinPrimaryForeignKey);

	/**
	 * @param $joinPrimaryForeignKey
	 * @return DMLClauseInterface
	 */
	public function leftJoin($joinPrimaryForeignKey);

	/**
	 * @param $joinPrimaryForeignKey
	 * @return DMLClauseInterface
	 */
	public function rightJoin($joinPrimaryForeignKey);

	/**
	 * @param array $fields
	 * @return ClauseSelectInterface
	 */
	public function groupBy(array $fields);

	/**
	 * @param array $fields
	 * @return ClauseSelectInterface
	 */
	public function orderBy(array $fields);

	/**
	 * @param int $startOffset
	 * @param int $endOffset
	 * @return ClauseSelectInterface
	 */
	public function limit($startOffset = 0, $endOffset = 30);
}