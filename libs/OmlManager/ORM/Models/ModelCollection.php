<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 24.04.2014
 * Time: 22:31
 * OmlManager\ORM\Models${NAME}
 */

namespace OmlManager\ORM\Models;

use OmlManager\ORM\Drivers\DriverInterface;
use OmlManager\ORM\Query\Types\CastingValue;

class ModelCollection {

	private $reader;

	private $driver;

	private $model;

	public function __construct(Reader $modelObject, DriverInterface $driverManager) {
		$this->reader = $modelObject;

		$this->model = $this->reader->getModel();

		$this->driver = $driverManager;
	}

	/**
	 * @param bool $fetchAssoc
	 * @return bool|mixed
	 */
	public function getCollection($fetchAssoc = false) {
		if ( $fetchAssoc ) {
			return $this->driver->fetchOne(\PDO::FETCH_ASSOC);
		}

		$refObject = new \ReflectionObject( $this->model );
		$this->model = $this->driver->fetchOne($refObject->getName());

		if ( empty($this->model) ) {

			return false;
		}

		return $this->model;
	}

	/**
	 * @param bool $fetchAssoc
	 * @return bool|mixed
	 */
	public function getCollections($fetchAssoc = false) {
		if ( $fetchAssoc ) {
			return $this->driver->fetchAll(\PDO::FETCH_ASSOC);
		}

		$refObject = new \ReflectionObject( $this->model );

		$this->model = $this->driver->fetchAll($refObject->getName());

		if ( empty($this->model) ) {

			return false;
		}

		return $this->model;
	}

	/**
	 * @return bool|mixed
	 */
	public function updateCollection() {
		$this->model = $this->driver->fetchAll('');

		if ( empty($this->model) ) {

			return false;
		}

		return $this->model;
	}
} 