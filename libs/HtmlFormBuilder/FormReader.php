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

	/**
	 * @param $modelObject
	 * @param array $formFieldsData
	 * @throws \OmlManager\ORM\Models\ReaderException
	 */
	public function __construct($modelObject, $formFieldsData = array()) {

		parent::__construct($modelObject);

		if ( $formFieldsData ) {
			$this->setModelFieldsValues($formFieldsData);
		}
	}

	/**
	 * @return mixed
	 */
	public function getModel() {
		return parent::getModel();
	}
}

interface FormReaderInterface {
	public function getModel();
}