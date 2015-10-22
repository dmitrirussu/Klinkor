<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 15.04.2014
 * Time: 22:37
 * ${NAMESPACE}${NAME} 
 */
namespace OmlManager\ORM\Query\DML\Clauses;

use OmlManager\ORM\Drivers\DriverInterface;
use OmlManager\ORM\Models\ModelCollection;
use OmlManager\ORM\Query\Expression\Expression;
use OmlManager\ORM\Query\Expression\ExpressionInterface;
use OmlManager\ORM\Models\Reader;
use OmlManager\ORM\SDB\SDBManagerConnections;

class SelectClause implements DMLClauseInterface, ClauseSelectInterface {

	/**
	 * @var Reader
	 */
	private $modelReader;

	private $_SELECT = 'SELECT ';
	private $_FROM = ' FROM ';
	private $_JOIN = ' INNER JOIN ';
	private $_LEFT = ' LEFT JOIN ';
	private $_RIGHT = ' RIGHT JOIN ';
	private $_WHERE = ' WHERE ';
	private $_GROUP_BY = ' GROUP BY ';
	private $_ORDER_BY = ' ORDER BY ';
	private $_HAVING = ' HAVING ';
	private $_LIMIT = ' LIMIT startOffset, endOffset';
	private $_AS = ' AS ';
	private $_ON = ' ON ';

	private $models = array();

	private $joinModel = null;

	private $defaultModelReaderAlias = 0;

	/**
	 * @var Expression
	 */
	private $expression;

	public function __construct($fields) {
		$defaultReaderObjectAlias = explode(',', $fields);

		if ( strlen($defaultReaderObjectAlias[0]) > 1 ) {

			$defaultReaderObjectAlias = explode('.', $defaultReaderObjectAlias[0]);

			if ( count($defaultReaderObjectAlias) > 1 ) {

				$this->defaultModelReaderAlias = $defaultReaderObjectAlias[0];
			}
		}

		$this->_SELECT .= $fields;
	}

	/**
	 * @param bool $fetchAssoc
	 * @return bool|mixed
	 * @throws \OmlManager\ORM\Models\ReaderException
	 */
	public function fetchOne($fetchAssoc = false) {
		$this->limit(0, 1);

		$modelCollection = new ModelCollection($this->getModelReader(),
			SDBManagerConnections::getManager($this->getModelReader()->getModelDataDriverConfName())
				->getDriver()->query($this->getQuery(), $this->expression->getPreparedStatement()));

		return $modelCollection->getCollection($fetchAssoc);
	}

	/**
	 * @return bool|mixed
	 * @throws \OmlManager\ORM\Models\ReaderException
	 */
	public function fetchAssocOne() {
		$this->limit(0, 1);
		try {
			$modelCollection = new ModelCollection($this->getModelReader(),
				SDBManagerConnections::getManager($this->getModelReader()->getModelDataDriverConfName())
					->getDriver()->query($this->getQuery(), $this->expression->getPreparedStatement()));
		}
		catch(\Exception $e) {
			throw $e;
		}
		return $modelCollection->getCollection(true);
	}

	/**
	 * @param bool $fetchAssoc
	 * @return bool|mixed
	 * @throws \OmlManager\ORM\Models\ReaderException
	 */
	public function fetchAll($fetchAssoc = false) {

		$modelCollection = new ModelCollection($this->getModelReader(),
			SDBManagerConnections::getManager($this->getModelReader()->getModelDataDriverConfName())
				->getDriver()->query($this->getQuery(), $this->expression->getPreparedStatement()));

		return $modelCollection->getCollections($fetchAssoc);
	}


	/**
	 * @return bool|mixed
	 * @throws \OmlManager\ORM\Models\ReaderException
	 */
	public function fetchAssocAll() {

		$modelCollection = new ModelCollection(
			$this->getModelReader(),
			SDBManagerConnections::getManager($this->getModelReader()->getModelDataDriverConfName())
				->getDriver()->query($this->getQuery(), $this->expression->getPreparedStatement())
		);

		return $modelCollection->getCollections(true);
	}

	public function model($object, $alias = null) {

		$this->modelReader = new Reader($object);
		$tableAlias = null;

		//set model
		if ( isset($alias) ) {

			$this->models[$alias] = $this->modelReader;
			$tableAlias = $this->_AS . $alias;
		}
		else {

			$this->models[] = $this->modelReader;
		}
		$this->joinModel = ($this->joinModel ? $this->_ON . $this->joinModel : $this->joinModel);

		$this->_FROM .= "`{$this->modelReader->getModelDataBaseName()}`" .'.'. "`{$this->modelReader->getModelTableName()}`" . $tableAlias . $this->joinModel;

		$this->joinModel = null;

		if ( !is_object($this->expression) ) {
			//Set Default Expression
			$exp = new Expression('1=1');
			$this->expression($exp);
		}

		return $this;
	}

	/**
	 * @return Reader
	 */
	public function getModelReader() {
		if ( isset($this->models[$this->defaultModelReaderAlias]) ) {
			return $this->models[$this->defaultModelReaderAlias];
		}

		$model = array_keys($this->models);

		return $this->models[reset($model)];
	}

	public function join($joinPrimaryForeignKey) {

		$this->joinModel = $joinPrimaryForeignKey;
		$this->_FROM .= $this->_JOIN;

		return $this;
	}

	public function leftJoin($joinPrimaryForeignKey) {

		$this->joinModel = $joinPrimaryForeignKey;
		$this->_FROM .= $this->_LEFT;

		return $this;
	}

	public function rightJoin($joinPrimaryForeignKey) {

		$this->joinModel = $joinPrimaryForeignKey;
		$this->_FROM .= $this->_RIGHT;

		return $this;
	}

	/**
	 * @param ExpressionInterface $exp
	 * @return ClauseSelectInterface
	 */
	public function expression(ExpressionInterface $exp) {
		$this->_WHERE = str_replace('1=1', '', $this->_WHERE);
		$this->expression = $exp;

		$this->expression->checkValuesTypeByModels($this->models);

		$this->_WHERE .= $exp->getExpression();

		return $this;
	}

	/**
	 * Group by array(field_name_one, field_name_two, field_name_three)
	 *
	 * @param array $fields
	 * @return $this|ClauseSelectInterface
	 */
	public function groupBy(array $fields) {

		$this->_WHERE .= $this->_GROUP_BY . implode(', ', $fields);

		return $this;
	}

	/**
	 * Order By array(field_name ASC, field_name DESC)
	 *
	 * @param array $fields
	 * @return $this|ClauseSelectInterface
	 */
	public function orderBy(array $fields) {

		$this->_WHERE .= $this->_ORDER_BY . implode(', ', $fields);

		return $this;
	}

	/**
	 * @param int $startOffset
	 * @param int $endOffset
	 * @return $this|ClauseSelectInterface
	 */
	public function limit($startOffset = 0, $endOffset = 30) {

		$this->_WHERE .= $this->_LIMIT = str_replace(array('startOffset', 'endOffset'), array($startOffset, $endOffset), $this->_LIMIT);

		return $this;
	}

	/**
	 * @return string
	 */
	private function getQuery() {
		return $this->_SELECT . $this->_FROM . $this->_WHERE;
	}

	public function __toString() {
		return $this->_SELECT . $this->_FROM . $this->_WHERE;
	}

	public function __destruct() {
		unset($this);
	}
} 