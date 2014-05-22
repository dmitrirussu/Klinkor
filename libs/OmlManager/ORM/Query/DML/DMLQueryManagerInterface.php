<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 21:38
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\Query\DML;

use OmlManager\ORM\Query\DDL\Clauses\NativeQuery;
use OmlManager\ORM\Query\DML\Clauses\ClauseSelectInterface;
use OmlManager\ORM\Query\DML\Clauses\DMLClauseInterface;
use OmlManager\ORM\Query\DML\Clauses\ExpressionClauseInterface;
use OmlManager\ORM\Query\DML\Clauses\InsertClause;
use OmlManager\ORM\Query\DML\Clauses\UpdateClause;
use OmlManager\ORM\Query\DML\Clauses\DeleteClause;

interface DMLQueryManagerInterface {
	/**
	 * @param string $fields
	 * @return DMLClauseInterface|ClauseSelectInterface|ExpressionClauseInterface
	 */
	public function select($fields = '*');

	/**
	 * @return InsertClause
	 */
	public function insert();

	/**
	 * @return UpdateClause
	 */
	public function update();

	/**
	 * @return DeleteClause
	 */
	public function delete();

	/**
	 * @return NativeQuery
	 */
	public function nativeQuery($sql, array $bindParamValues = array(), $object = null);
} 