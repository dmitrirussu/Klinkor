<?php
function __autoload( $className ) {

	require_once '../../../libs/'.$className .'.php';
}

$dataBaseName = $argv[1];
$entityName = (isset($argv[2]) ? $argv[2] : null);

echo "\nStart!";

if ( $entityName ) {

	\OmlManager\ORM\SchemaEntitiesGenerator\OmlEntitiesGeneratorFactory::create($dataBaseName, realpath(__DIR__))
		->generateEntity($entityName);
}
else{

	\OmlManager\ORM\SchemaEntitiesGenerator\OmlEntitiesGeneratorFactory::create($dataBaseName, realpath(__DIR__))
		->generateSchemaEntities();
}
echo "\ndone!";