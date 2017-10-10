<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 28.04.2014
 * Time: 18:47
 * OmlManager\ORM\Query\DDL\Clauses${NAME}
 */

namespace OmlManager\ORM\Query\DDL\Clauses;


class Table {
	private $_TABLE = 'TABLE ';

	private $driver;

	public function __construct($name, $driver) {

		$this->driver = $driver;
	}

	public function addAttribute($fieldName, $type, $length = null, $isPrimaryKey = false, $isAutoIncrement = false) {

		return $this;
	}

	public function changeAttribute($fieldName, $type, $length = null, $isPrimaryKey = false, $isAutoIncrement = false) {

		return $this;
	}
} 