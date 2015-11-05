<?php
/**
 * Created by Dumitru Russu.
 * Date: 27.06.2014
 * Time: 22:07
 * FormsGenerator${NAME}
 */

namespace HtmlFormBuilder;

use HtmlFormBuilder\Interfaces\IFormBuilder;
use HtmlFormBuilder\Interfaces\IFormValidator;
use OmlManager\ORM\Query\Types\ValueTypes;
use HtmlFormBuilder\Translator\FormTranslations;
use HtmlFormBuilder\Validator\FormValidator;
use HtmlFormBuilder\Exceptions\FormBuilderException;
use HtmlFormBuilder\Utils\FormBuilderUtils;

class FormBuilder implements IFormBuilder {

	private $errors = array();

	private $usedFields = array();
	private $unusedFields = array();
	private $requiredFields = array();
	private $FORMS_UNIFY = false;
	private $MODELS = array();
	private $modelObjects = array();
	private $HTML = '';
	private $simpleForm = false;
	private $formObjects = '';

	private $mainModelObject = array();
	private $fieldsAppended = array();
	private $fieldsClassName = array();
	private $fieldsLabel = array();
	private $fieldsType = array();

	private $selectBoxData = array();

	private $formContainerClass = null;
	private $formAction = null;

	/**
	 * @var ModelFieldsReader
	 */
	private $reader;
	private $fieldValue;

	/**
	 * @var FormValidator
	 */
	private $formValidator;

	/**
	 * @var \OmlManager\ORM\Models\Reader
	 */
	private $DEFAULT_MODEL;


	private static $OPTION_PROTOTYPE = array(
		'section_class' => 'col-md-6',
		'action' => '',
		'method' => 'post',
		'fields' => array(),
		'buttons' => array(
			'reset' => array('active' => true, 'keyword' => 'Reset', 'type' => FormObjectTypes::RESET),
			'submit' => array('active' => true, 'keyword' => 'Submit', 'type' => FormObjectTypes::SUBMIT),
		));


	/**
	 * @param null $mainModel
	 * @param array $options
	 * @param bool $simpleForm
	 * @throws FormBuilderException
	 */
	public function __construct($mainModel = null,
								array $options = array(
									'section_class' => 'col-md-6',
									'action' => '',
									'method' => 'post',
									'fields' => array(),
									'buttons' => array(
										'reset' => array('active' => true, 'keyword' => 'Reset', 'type' => FormObjectTypes::RESET),
										'submit' => array('active' => true, 'keyword' => 'Submit', 'type' => FormObjectTypes::SUBMIT),
									)), $simpleForm = false) {

		if ( empty($mainModel) ) {
			throw new FormBuilderException('Model Cannot Be Empty');
		}

		$this->simpleForm = $simpleForm;

		$this->HTML = null;
		$this->modelObjects[] = $this->mainModelObject = $mainModel;

		$this->MODELS[] = $this->DEFAULT_MODEL = array(
			'model' => $mainModel,
			'options' => (empty($options) ? self::$OPTION_PROTOTYPE : $options)
		);
	}

	/**
	 * Add model to Form Builder
	 * @param $model
	 * @param array $options
	 * @return $this|mixed
	 * @throws FormBuilderException
	 */
	public function addModel($model, array $options = array(
		'section_class' => 'col-md-6',
		'action' => '',
		'method' => 'post',
		'fields' => array(),
		'buttons' => array(
			'reset' => array('active' => true, 'keyword' => 'Reset', 'type' => FormObjectTypes::RESET),
			'submit' => array('active' => true, 'keyword' => 'Submit', 'type' => FormObjectTypes::SUBMIT),
		))) {

		if ( empty($model) ) {

			throw new FormBuilderException('Form model param cannot be empty');
		}

		$this->modelObjects[] = $model;

		$this->MODELS[] = array(
			'model' => $model,
			'options' => $options
		);

		return $this;
	}


	/**
	 * @param $fieldName
	 * @param string $value
	 * @param string $appendAfterFieldName
	 * @param null $modelObject
	 * @return $this
	 */
	public function addField($fieldName, $value = '', $appendAfterFieldName = 'end' /*end|top|field_name*/, $modelObject = null) {
		$this->fieldsAppended[$fieldName] = array(
			'type' => 'varchar',
			'field_name' => $fieldName,
			'value' => $value,
			'position' => $appendAfterFieldName,
			'model' => $this->mainModelObject
		);
		return $this;
	}

	/**
	 * @param $fieldName
	 * @param string $fieldKey
	 * @param array $fields
	 * @param array $selectBoxData
	 * @return $this
	 */
	public function addSelectBoxData($fieldName, $fieldKey = '', $fields = array(), $selectBoxData = array(), $selectedValues = array()) {
		$this->selectBoxData[$fieldName] = array(
			'key' => $fieldKey,//field Key On Option Value
			'fields' => $fields, //Fields Name on The Option
			'data' => $selectBoxData, //Select Box Option Name
			'selected_values' => $selectedValues, //Select Box Option Name
		);
		return $this;
	}

	/**
	 * @param $fieldName
	 * @param $label
	 * @return $this
	 */
	public function addFieldLabel($fieldName, $label) {

		$this->fieldsLabel[$fieldName] = $label;

		return $this;
	}

	/**
	 * @param $fieldName
	 * @param $className
	 * @return $this
	 */
	public function addFieldClassName($fieldName, $className) {
		$this->fieldsClassName[$fieldName] = $className;
		return $this;
	}

	/**
	 * This Method is designed to be changed Type of Field example from text to file
	 * @param $fieldName
	 * @param $type
	 * @return $this
	 */
	public function addFieldType($fieldName, $type) {
		$this->fieldsType[$fieldName] = $type;
		return $this;
	}

	/**
	 * Set Model is Deprecated
	 * @param $model
	 * @param array $options
	 * @deprecated Can be used another method addModel($model, $options)
	 * @return $this
	 * @throws FormBuilderException
	 */
	public function setModel($model, array $options = array()) {
		$this->addModel($model, $options);
		return $this;
	}


	/**
	 * Set Required Fields
	 * @param $args
	 * @return $this
	 */
	public function setRequiredFields($args) {
		$this->requiredFields = $args;

		if ( !is_array($args) ) {
			$this->requiredFields = func_get_args();
		}

		return $this;
	}

	/**
	 * Get required Fields
	 * @return array
	 */
	public function getRequiredFields() {
		return $this->requiredFields;
	}


	/**
	 * Set Used Fields
	 * @param array $fields OR strings, $arg1, $arg2, ...
	 * @return $this
	 */
	public function setUsedFields($fields) {
		$this->usedFields = $fields;

		if ( !is_array($fields) ) {
			$this->usedFields = func_get_args();
		}

		return $this;
	}

	/**
	 * Set UnUsed Fields
	 * @param $args
	 * @return $this|mixed
	 */
	public function setUnusedFields($args) {

		$this->unusedFields = $args;

		if ( !is_array($args) ) {
			$this->unusedFields = func_get_args();
		}

		return $this;
	}

	/**
	 * Unset Unused Fields
	 * @deprecated Recommend for using setUnusedFields
	 * @param $args
	 * @return $this
	 */
	public function unsetUnusedFields($args) {

		return $this->setUnusedFields($args);
	}


	/**
	 * @return IFormValidator
	 */
	public function validator() {

		return new FormValidator($this->modelObjects, $this->getRequiredFields());
	}


	public function getForm() {
		$this->buildForms();
		return $this->HTML;
	}


	public function show() {
		$this->buildForms();
		return print($this->HTML);
	}

	/**
	 * @param array      $array
	 * @param int|string $position
	 * @param mixed      $insert
	 */
	function arrayInsert(&$array, $position, $insert)
	{
		if (is_int($position)) {
			array_splice($array, $position, 0, $insert);
		} else {
			$pos   = array_search($position, array_keys($array));
			$array = array_merge(
				array_slice($array, 0, $pos),
				$insert,
				array_slice($array, $pos)
			);
		}
	}

	/**
	 * Build Forms
	 * @throws FormBuilderException
	 */
	private function buildForms() {
		$this->HTML = null;
		$this->formObjects = null;

		if ( empty($this->MODELS) ) {
			throw new FormBuilderException('Form model param cannot be empty');
		}

		$lengthOfModels = count($this->MODELS);

		$i = 0;
		foreach($this->MODELS as $model) {
			$this->reader = new ModelFieldsReader($model['model']);
			$properties = $this->reader->getModelPropertiesTokens();

			if ( $model['model'] )

				if ( $properties ) {
					$propertiesAdd = $properties;

					$a = 0;
					foreach($propertiesAdd AS $property) {
						foreach($this->fieldsAppended AS $field) {
							if($field['position'] === $property['field'] && $field['model'] instanceof $model['model']) {
								$this->arrayInsert($properties, $a+1, array(array(
									'var' => '$'.$field['field_name'],
									'field' => $field['field_name'],
									'value' => $field['value'],
									'type' => $field['type'],
									'length' => 1000,
									'skip' => true
								)));
								unset($this->fieldsAppended[$field['field_name']]);
							}
							else if ($field['position'] === 'top' && $field['model'] instanceof $model['model']) {
								$this->arrayInsert($properties, 0, array(array(
									'var' => '$'.$field['field_name'],
									'field' => $field['field_name'],
									'value' => $field['value'],
									'type' => $field['type'],
									'length' => 1000,
									'skip' => true
								)));
								unset($this->fieldsAppended[$field['field_name']]);
							}
							else if ($field['position'] === 'end' && $field['model'] instanceof $model['model']) {
								$this->arrayInsert($properties, count($properties), array(array(
									'var' => '$'.$field['field_name'],
									'field' => $field['field_name'],
									'value' => $field['value'],
									'type' => $field['type'],
									'length' => 1000,
									'skip' => true
								)));
								unset($this->fieldsAppended[$field['field_name']]);
							}
						}
						$a++;
					}
				}

			if ($properties) {
				foreach($properties as $property) {
					if ( !isset($property['skip']) ) {
						$this->fieldValue = htmlentities($this->reader->getValueByFieldName($property['field']));
					}
					else {
						$this->fieldValue = $property['value'];
					}

					//skip fields which are not required or are primary key
					if (in_array($property['field'], $this->unusedFields)) {
						continue;
					}

					if ( $this->usedFields ) {
						if ( !in_array($property['field'], $this->usedFields)) {
							continue;
						}
					}

					if ( !isset($property['type']) ) {
						throw new FormBuilderException('Missing property type, fieldName: ' . $property['field']);
					}

					$propertyFieldName = $this->reader->getModelTableName().'['.$property['field'].']';
					if ( $lengthOfModels > 1 ) {
						$propertyFieldName = $this->reader->getModelTableName().'['.$i.']['.$property['field'].']';
					}

					if ( $this->simpleForm ) {

						$propertyFieldName = $property['field'];
					}

					$options = $model['options'];

					//check exist property type inside of fields
					$propertyType = (isset($options['fields'][$property['field']]['type']) ?
						$options['fields'][$property['field']]['type'] : (isset($this->fieldsType[$property['field']]) ?
							$this->fieldsType[$property['field']] : $property['type']));

					switch($propertyType) {
						case FormObjectTypes::HIDDEN: {
							$this->buildInputTextObject($model, $propertyFieldName, $property, FormObjectTypes::HIDDEN);
							break;
						}
						case FormObjectTypes::PASSWORD: {
							$this->buildInputTextObject($model, $propertyFieldName, $property, FormObjectTypes::PASSWORD);
							break;
						}
						case FormObjectTypes::CHECKBOX:
						case ValueTypes::VALUE_TYPE_TINYINT:
						case ValueTypes::VALUE_TYPE_BIT:
						case ValueTypes::VALUE_TYPE_BOOLEAN:
						case ValueTypes::VALUE_TYPE_BOOL: {
							$this->buildInputTextObject($model, $propertyFieldName, $property, FormObjectTypes::CHECKBOX);

							break;
						}
						case ValueTypes::VALUE_TYPE_SMALLINT:
						case ValueTypes::VALUE_TYPE_MEDIUMINT:
						case ValueTypes::VALUE_TYPE_BIGINT:
						case ValueTypes::VALUE_TYPE_INT: {
							$this->buildInputTextObject($model, $propertyFieldName, $property, FormObjectTypes::NUMBER);

							break;
						}
						case ValueTypes::VALUE_TYPE_DECIMAL:
						case ValueTypes::VALUE_TYPE_DOUBLE:
						case ValueTypes::VALUE_TYPE_REAL:
						case ValueTypes::VALUE_TYPE_FLOAT: {
							$this->buildInputTextObject($model, $propertyFieldName, $property, FormObjectTypes::NUMBER);

							break;
						}
						case FormObjectTypes::SELECT:
						case ValueTypes::VALUE_TYPE_SET:
						case ValueTypes::VALUE_TYPE_ENUM: {
							$this->buildEnumSelectBox($model, $property, $propertyFieldName);

							break;
						}
						case FormObjectTypes::FILE:
						case ValueTypes::VALUE_TYPE_BLOB:
						case ValueTypes::VALUE_TYPE_LONGBLOB: {
							$this->buildInputTextObject($model, $propertyFieldName, $property, FormObjectTypes::FILE);

							break;
						}
						case ValueTypes::VALUE_TYPE_TIMESTAMP:
						case ValueTypes::VALUE_TYPE_DATETIME:
						case ValueTypes::VALUE_TYPE_DATE:
						case ValueTypes::VALUE_TYPE_TIME: {
							$this->buildInputTextObject($model, $propertyFieldName, $property, FormObjectTypes::DATETIME);

							break;
						}
						case ValueTypes::VALUE_TYPE_TEXT:
						case ValueTypes::VALUE_TYPE_MEDIUMTEXT:
						case ValueTypes::VALUE_TYPE_LONGTEXT: {
							$this->buildInputTextObject($model, $propertyFieldName, $property, FormObjectTypes::TEXTAREA);

							break;
						}
						case ValueTypes::VALUE_TYPE_STRING:
						case ValueTypes::VALUE_TYPE_VARCHAR:
						case ValueTypes::VALUE_TYPE_CHAR: {
							$this->buildInputTextObject($model, $propertyFieldName, $property);

							break;
						}
						default: {
							$this->buildInputTextObject($model, $propertyFieldName, $property);

							break;
						}
					}
				}

				if ( !$this->FORMS_UNIFY ) {

					$this->buildForm($this->reader, $model);
				}
			}
			$i++;
		}

		if ( $this->FORMS_UNIFY ) {

			$this->buildForm(new ModelFieldsReader($this->DEFAULT_MODEL['model']), $this->DEFAULT_MODEL);
		}
	}

	/**
	 * @param $model
	 * @param $propertyFieldName
	 * @param $property
	 * @param string $type
	 */
	private function buildInputTextObject($model, $propertyFieldName, $property, $type = FormObjectTypes::TEXT) {
		$object = null;
		$classes = 'form-group';
		$attribute = null;
		$checked = null;
		$objectClassName = 'form-control ' . (isset($property['field']) && isset($this->fieldsClassName[$property['field']]) ? $this->fieldsClassName[$property['field']] : '');
		$fieldLabel = (isset($this->fieldsLabel[$property['field']]) ? $this->fieldsLabel[$property['field']] : $property['field']);
		$fieldObjectOptions = (isset($model['options']['fields'][$property['field']]) ? $model['options']['fields'][$property['field']] : '');

		if ( isset($property['field']) ) {

			if ( isset($fieldObjectOptions['class'])) {
				$objectClassName = $fieldObjectOptions['class'];
			}

			if ( isset($fieldObject['attribute']) ) {
				$attribute = $fieldObjectOptions['attribute'];
			}
			if ( isset($fieldObjectOptions['attributes']) ) {
				$attribute = $fieldObjectOptions['attributes'];
			}

			if ( isset($fieldObjectOptions['label']) ) {
				$fieldLabel = $fieldObjectOptions['label'];
			}
		}


		switch($type) {
			case FormObjectTypes::TEXT: {
				$object =
					FormObjects::label(FormTranslations::t('form', $fieldLabel), ' for="'.$propertyFieldName.'"').
					FormObjects::input(FormObjectTypes::TEXT, $propertyFieldName, $this->fieldValue,
						'class="'.$objectClassName.'" id="'.$propertyFieldName.'" ' . $attribute);

				break;
			}
			case FormObjectTypes::PASSWORD: {
				$object =
					FormObjects::label(FormTranslations::t('form', $fieldLabel), ' for="'.$propertyFieldName.'"').
					FormObjects::input(FormObjectTypes::PASSWORD, $propertyFieldName, $this->fieldValue,
						'class="'.$objectClassName.'" id="'.$propertyFieldName.'" ' . $attribute);

				break;
			}
			case FormObjectTypes::CHECKBOX: {
				if ($this->fieldValue) {
					$checked = FormObjects::CHECKED;
				}

				$object = FormObjects::label(FormObjects::input($type, $propertyFieldName, $this->fieldValue,
						'id="'.$propertyFieldName.'" ' . $checked . ' ' . $attribute).
					FormTranslations::t('form', $fieldLabel), ' for="'.$property['field'].'"');

				$classes = 'checkbox';
				break;
			}
			case FormObjectTypes::TEXTAREA: {

				$object .= FormObjects::label(FormTranslations::t('form', $fieldLabel), ' for="'.$propertyFieldName.'"') .
					FormObjects::textArea($propertyFieldName, $this->fieldValue, false,'class="'.$objectClassName.'"' . ' id="'.$propertyFieldName.'" ' . $attribute);

				break;
			}
			default: {
				$objectClassName = ($type === FormObjectTypes::FILE ? '' : $objectClassName);
				$label = FormObjects::label(FormTranslations::t('form', $fieldLabel), ' for="'.$property['field'].'"');

				if ( (isset($property['primary_key']) || isset($property['auto_increment']) ) || $type === FormObjectTypes::HIDDEN) {

					$type = FormObjectTypes::HIDDEN;
					$label = null;
				}

				$input = FormObjects::input($type, $propertyFieldName, $this->fieldValue, 'class="'.$objectClassName.'" id="'.$property['field'].'"' . $attribute);

				$object =  $label . $input;
				break;
			}
		}

		$this->formObjects .= FormObjects::fieldset($object, 'class="'.$classes.'"');
	}



	/**
	 * Build Enum Select Box
	 * @param $model
	 * @param $property
	 * @param $propertyFieldName
	 * @param $objects
	 */
	private function buildEnumSelectBox($model, $property, $propertyFieldName) {
		$reader = new ModelFieldsReader($model['model']);
		$optionsResult = null;
		$attribute = null;
		$multiple = null;
		$classes = 'form-group';
		if ( isset($this->selectBoxData[$property['field']]) ) {
			$fieldOptions = $this->selectBoxData[$property['field']];
		} else {
			$fieldOptions = (isset($model['options']['fields'][$property['field']]) ? $model['options']['fields'][$property['field']] : array());
		}
		$selectedValues = isset($fieldOptions['selected_values']) ? $fieldOptions['selected_values'] : array();


		$methodKey = (isset($fieldOptions['key']) ? $fieldOptions['key'] : $property['field']);


		//get Data from Enum
		if ( !isset($fieldOptions['type']) && !isset($this->fieldsType[$property['field']])) {

			$optionsResult = $this->generateOptionsFromEnum($methodKey, $selectedValues, $reader);
		}
		else {

			$optionsResult = $this->generateOptionsFromObjectList($methodKey, $selectedValues, $fieldOptions);
		}

		if ( isset($this->fieldsLabel[$property['field']]) ) {
			$fieldName = $this->fieldsLabel[$property['field']];
		}
		else {
			$fieldName = (isset($fieldOptions['keyword']) ? $fieldOptions['keyword'] : $property['field']);
		}

		$objectClassName = 'form-control ';// . (isset($this->fieldsClassName[$property['field']]) ? $this->fieldsClassName[$property['field']] : '');

		if ( isset($property['field']) && isset($fieldOptions['class']) ) {
			$objectClassName = $fieldOptions['class'];
		}


		if ( isset($property['field']) ) {
			if (isset($fieldOptions['attribute']) ) {
				$attribute = $fieldOptions['attribute'];
			}

			if ( isset($fieldOptions['multiple']) ) {
				$multiple = ' multiple="" ';
				$propertyFieldName .= '[]';
			}
		}

		$object = FormObjects::label(FormTranslations::t('form', $fieldName), ' for="'.$propertyFieldName.'"').
			FormObjects::select($propertyFieldName, $optionsResult, 'class="'.$objectClassName.'"' . ' id="'.$propertyFieldName.'"' . ' ' . $attribute . $multiple);

		$this->formObjects .= FormObjects::fieldset($object, 'class="'.$classes.'"');
	}

	/**
	 * Generate Options From Model Enum List array
	 * @param $methodKey
	 * @param $selectedValues
	 * @param ModelFieldsReader $reader
	 * @return string
	 */
	private function generateOptionsFromEnum($methodKey, $selectedValues, ModelFieldsReader $reader) {
		$optionsResult = '';
		$methodName = FormBuilderUtils::getGetterMethod($methodKey).'List';
		$options = $reader->getModel()->{$methodName}();

		if ( $options ) {
			foreach($options AS $option) {
				$selected = '';
				if ( $this->fieldValue == $option || $this->checkValueInArray($methodKey, $option, $selectedValues)) {
					$selected = true;
				}
				$optionsResult .= FormObjects::option(ucfirst($option), $option, $selected);
			}
		}

		return $optionsResult;
	}


	/**
	 * @param $methodKey
	 * @param $selectedValues
	 * @param $optionFields
	 * @return string
	 */
	private function generateOptionsFromObjectList($methodKey, $selectedValues, $optionFields) {
		$optionsResult = '';
		//Get Data from Select
		$methodName = FormBuilderUtils::getGetterMethod($methodKey);
		$optionsData = isset($optionFields['data']) ? $optionFields['data'] : array();

		if ( $optionsData ) {
			foreach($optionsData AS $value => $optionData) {
				$optionDataKey = $optionData;
				$isObject = false;
				$selected = '';

				if ( $this->fieldValue == $value && !is_object($optionData)) {
					$selected = true;
				}

				if ( is_object($optionData) ) {

					$value = $optionData->{$methodName}();
					if ( isset($optionFields['fields']) && is_array($optionFields['fields']) ) {
						$resultFieldsData = array();
						foreach($optionFields['fields'] AS $fieldName ) {
							$fieldMethodName = FormBuilderUtils::getGetterMethod($fieldName);

							$resultFieldsData[] = FormTranslations::t('form', $optionData->{$fieldMethodName}());
						}
						$optionDataKey = implode('|', $resultFieldsData);
					}
					$isObject = true;

					if ( $this->fieldValue == $value || $this->checkValueInArray($methodKey, $value, $selectedValues)) {
						$selected = true;
					}
				}

				$optionsResult .= FormObjects::option(($isObject ? $optionDataKey : FormTranslations::t('form', $optionDataKey)), $value, $selected);
			}
		}

		return $optionsResult;
	}

	/**
	 * Check Value In Array
	 * @param $fieldName
	 * @param $value
	 * @param array $selectedValues
	 * @return bool
	 */
	private function checkValueInArray($fieldName, $value, array $selectedValues) {

		if( is_array($selectedValues) ) {
			if ( !is_object(end($selectedValues))) {
				return in_array($value, $selectedValues);
			}
			else {
				$methodName = FormBuilderUtils::getGetterMethod($fieldName);
				foreach($selectedValues as $selectedObject) {
					if ( $value === $selectedObject->{$methodName}()) {
						return true;
					}
				}
			}
		}

		return false;
	}

	public function addFormContainerClassName($className) {
		$this->formContainerClass = $className;
	}

	public function addFormAction($action) {
		$this->formAction = $action;
	}

	/**
	 * Build Model Form
	 * @param ModelFieldsReader $reader
	 * @param $model
	 */
	private function buildForm(ModelFieldsReader $reader, $model) {

		$action = (isset($model['options']['action']) ? $model['options']['action'] : null);
		$action = ($this->formAction ? $this->formAction : $action);
		$method = (isset($model['options']['method']) ? $model['options']['method'] : FormObjects::FORM_METHOD_POST);
		if ( $this->formContainerClass ) {
			$sectionClass = $this->formContainerClass;
		}
		else {
			$sectionClass = (isset($model['options']['section_class']) ? $model['options']['section_class'] : 'col-md-6');
		}

		$buttonObjects = $this->generateButtons($model, $reader);

		$form = FormObjects::form($reader->getModelTableName(), $method, $this->formObjects . $buttonObjects, $action);
		$this->HTML .= FormObjects::formContainer($form, $sectionClass);

		$this->formObjects = null;
	}

	/**
	 * Generate Buttons
	 * @param $model
	 * @param ModelFieldsReader $reader
	 * @return null|string
	 */
	private function generateButtons($model, ModelFieldsReader $reader) {
		$buttonObjects = null;
		if (isset($model['options']['buttons'])) {

			foreach($model['options']['buttons'] as $key => $button) {
				$label = (isset($button['keyword']) ? $button['keyword'] : (isset($button['label']) ? $button['label'] : ''));
				$name = (isset($button['name']) ? $button['name'] : $reader->getModelTableName());

				if ( $key === 'submit' ) {

					$buttonObjects .= FormObjects::button($key, $name.'_submit',
						FormTranslations::t('form', $label), 'class="btn btn-primary"');
				}
				elseif ( $key === 'reset' && isset($button['active']) && $button['active']) {

					$buttonObjects .= FormObjects::button($key, $name.'_reset',
						FormTranslations::t('form', $label), 'class="btn btn-default"');

				}
				else {
					$buttonType = (isset($button['type']) ? $button['type'] : FormObjectTypes::BUTTON);
					$className = (isset($button['class']) ? $button['class'] : '');

					$buttonObjects .= FormObjects::button($buttonType, 'btn_'.$name,
						FormTranslations::t('form', $label),  'class="btn btn-primary '.$className.'"');
				}
			}
		}

		return $buttonObjects;
	}

	/**
	 * Forms Unify
	 * @return $this
	 */
	public function unify() {
		$this->FORMS_UNIFY = true;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getErrors() {

		return $this->errors;
	}


	public function __toString() {

		return $this->HTML;
	}

	public function __destruct() {
		unset($this->HTML);
		unset($this->MODELS);
	}
} 