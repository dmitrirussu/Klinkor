<?php
/**
 * Created by Dumitru Russu.
 * Date: 28.06.2014
 * Time: 00:09
 * HtmlFormGenerator${NAME} 
 */

namespace HtmlFormBuilder\Validator;


use HtmlFormBuilder\Interfaces\IFormValidator;
use HtmlFormBuilder\ModelFieldsReader;
use HtmlFormBuilder\Utils\FormBuilderUtils;

class FormValidator implements IFormValidator {

	private $models;

	private $requiredFields;

	private $errors;


	public function __construct(array $models, $requiredFields) {
		$this->models = $models;
		$this->requiredFields = $requiredFields;
	}

	public function isValid() {

		if ( $this->models ) {
			foreach($this->models as $model) {
				$reader = new ModelFieldsReader($model);
				$modelTokens = array_map(function($property){
					return $property['field'];
				}, $reader->getModelPropertiesTokens());

				if ( $modelTokens ) {
					foreach($this->requiredFields as $property) {
						if ( in_array($property, $modelTokens) ) {
							$getValue = FormBuilderUtils::getGetterMethod($property);
							$value = trim($model->$getValue());
							if ( !$value && (!is_bool($value) && !is_int($value) && !is_float($value) && (is_string($value) && strlen($value) <= 0))) {
								$this->errors[$property] = 'Missing field value, property name: ' . $property;
							}
						}
						else {
							$this->errors[$property] = 'Missing property Name: ' . $property;
						}
					}
				}
			}
		}

		if ( empty($this->errors) ) {
			return true;
		}

		return false;
	}

	public function getErrors() {

		return $this->errors;
	}
}

