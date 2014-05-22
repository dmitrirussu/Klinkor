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
use OmlManager\ORM\Query\DML\Clauses\SelectClause;
use OmlManager\ORM\Query\DML\Clauses\InsertClause;
use OmlManager\ORM\Query\DML\Clauses\UpdateClause;
use OmlManager\ORM\Query\DML\Clauses\DeleteClause;

class DMLQueryManager implements DMLQueryManagerInterface {


	public function __construct() {}

	/**
	 * @param string $fields
	 * @return DMLClauseInterface|ClauseSelectInterface|ExpressionClauseInterface
	 */
	public function select($fields = '*') {

		return new SelectClause($fields);
	}

	/**
	 * @return InsertClause
	 */
	public function insert() {

		return new InsertClause();
	}

	/**
	 * @return UpdateClause
	 */
	public function update() {

		return new UpdateClause();
	}

	/**
	 * @return DeleteClause
	 */
	public function delete() {

		return new DeleteClause();
	}

	/**
	 * @param $object
	 * @param $sql
	 * @param array $bindParamValues
	 * @return NativeQuery
	 */
	public function nativeQuery($sql, array $bindParamValues = array(), $object = null) {

		return new NativeQuery($sql, $bindParamValues, $object);
	}
} 