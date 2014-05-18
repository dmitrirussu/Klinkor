<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 25.04.2014
 * Time: 08:39
 * OmlManager\ORM\Console${NAME}
 */

namespace OmlManager\ORM\SchemaEntitiesGenerator;


class SchemaGenerator extends EntityGenerator {
	private $entities;

	public function generateSchemaEntities() {
		parent::generateSchemaEntities();

		//create Entity Class
		$this->entities = $this->getAllEntitiesName();

		if ( $this->entities ) {
			foreach($this->entities AS $entity) {

				$this->generateEntity($entity->table_name);
			}
		}
	}
}