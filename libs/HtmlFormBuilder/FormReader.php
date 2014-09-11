<?php
/**
 * Created by Dumitru Russu.
 * Date: 06.07.2014
 * Time: 22:22
 * HtmlFormBuilder${NAME} 
 */

namespace HtmlFormBuilder;


use OmlManager\ORM\Models\Reader;

class FormReader extends Reader implements FormReaderInterface {

	public function setModelFieldsValues(array $fields) {

		parent::setModelFieldsValues($fields);

		return $this;
	}

	public function getModel() {

		return parent::getModel();
	}
}

interface FormReaderInterface {
	public function setModelFieldsValues(array $fields);
	public function getModel();
}