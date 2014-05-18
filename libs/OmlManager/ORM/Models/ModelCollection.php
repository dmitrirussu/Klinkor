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
	 * @return mixed
	 */
	public function getCollection() {
		$refObject = new \ReflectionObject( $this->model );
		$this->model = $this->driver->fetchOne($refObject->getName());

		if ( empty($this->model) ) {

			return false;
		}

		return $this->model;
	}

	/**
	 * @return bool|array
	 */
	public function getCollections() {
		$refObject = new \ReflectionObject( $this->model );

		$this->model = $this->driver->fetchAll($refObject->getName());

		if ( empty($this->model) ) {

			return false;
		}

		return $this->model;
	}

	public function updateCollection() {
		$this->model = $this->driver->fetchAll('');

		if ( empty($this->model) ) {

			return false;
		}

		return $this->model;
	}
} 