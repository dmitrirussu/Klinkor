<?php
/**
 * Created by Dumitru Russu.
 * Date: 27.06.2014
 * Time: 22:08
 * ${NAMESPACE}\${NAME} 
 */
namespace HtmlFormBuilder;

class ModelFieldsReader extends \OmlManager\ORM\Models\Reader {

	private $model;


	public function __construct($model) {

		parent::__construct($model);
	}
}