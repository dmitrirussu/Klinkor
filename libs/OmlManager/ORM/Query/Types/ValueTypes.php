<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 26.04.2014
 * Time: 10:32
 * OmlManager\ORM\Query\Types${NAME}
 */

namespace OmlManager\ORM\Query\Types;


class ValueTypes {
	//number
	const VALUE_TYPE_INT = 'int';
	const VALUE_TYPE_SMALLINT = 'smallint';
	const VALUE_TYPE_MEDIUMINT = 'mediumint';
	const VALUE_TYPE_BIGINT = 'bigint';
	const VALUE_TYPE_DECIMAL = 'decimal';
	const VALUE_TYPE_FLOAT = 'float';
	const VALUE_TYPE_DOUBLE = 'double';
	const VALUE_TYPE_REAL = 'real';

	//string
	const VALUE_TYPE_CHAR = 'char';
	const VALUE_TYPE_VARCHAR = 'varchar';
	const VALUE_TYPE_STRING = 'string';
	const VALUE_TYPE_MEDIUMTEXT = 'mediumtext';
	const VALUE_TYPE_TEXT = 'text';
	const VALUE_TYPE_LONGTEXT = 'longtext';

	//boolean
	const VALUE_TYPE_BIT = 'bit';
	const VALUE_TYPE_TINYINT = 'tinyint';
	const VALUE_TYPE_BOOL = 'bool';
	const VALUE_TYPE_BOOLEAN = 'boolean';
	const VALUE_TYPE_NULL = 'null';
	const VALUE_TYPE_EMPTY = 'empty';

	//file
	const VALUE_TYPE_BLOB = 'blob';
	const VALUE_TYPE_LONGBLOB = 'longblob';

	//array
	const VALUE_TYPE_ENUM = 'enum';
	const VALUE_TYPE_SET = 'set';
	const VALUE_TYPE_TIMESTAMP = 'timestamp';
	const VALUE_TYPE_DATETIME = 'datetime';
	const VALUE_TYPE_DATE = 'date';
	const VALUE_TYPE_TIME = 'time';
}