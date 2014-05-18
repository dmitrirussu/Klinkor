<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 21:43
 * ${NAMESPACE}${NAME} 
 */
namespace OmlManager\ORM\Query\DDL;

interface DDLQueryManagerInterface {

	/**
	 * @return Clauses\ClauseInterface
	 */
	public function create();

	/**
	 * @return Clauses\ClauseInterface
	 */
	public function alter();

	/**
	 * @return Clauses\ClauseInterface
	 */
	public function clean();

	/**
	 * @return Clauses\ClauseInterface
	 */
	public function drop();
}
