<?php
/**
 * Created by Dumitru Russu.
 * Date: 09.05.2014
 * Time: 15:39
 * OmlManager\ORM\SchemaEntitiesGenerator${NAME} 
 */

namespace OmlManager\ORM\SchemaEntitiesGenerator;


class OmlEntitiesGeneratorFactory {
	public static function create($dataBaseConfName, $path, $nameSpace = '') {

		return new SchemaGenerator($dataBaseConfName, $path, $nameSpace);
	}
} 