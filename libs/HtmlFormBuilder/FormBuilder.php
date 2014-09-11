<?php
/**
 * Created by Dumitru Russu.
 * Date: 27.06.2014
 * Time: 22:07
 * FormsGenerator${NAME} 
 */

namespace HtmlFormBuilder;


use AppLauncher\FormValidator\FormValidator;
use OmlManager\ORM\Query\Types\ValueTypes;

class FormBuilder {

	private $errors = array();

	private $usedFields = array();
	private $unusedFields = array();
	private $FORMS_UNIFY = false;
	private $MODELS = array();
	private $HTML = '';
	private $simpleForm = false;

	/**
	 * @var ModelFieldsReader
	 */
	private $reader;
	private $fieldValue;

	/**
	 * @var \OmlManager\ORM\Models\Reader
	 */
	private $DEFAULT_MODEL;

	/**
	 * @param null $model
	 * @param array $options
	 * @param bool $simpleForm
	 * @throws FormBuilderException
	 */
	public function __construct($model = null,
								array $options = array(
									'section_class' => 'col-md-6',
									'action' => '',
									'method' => 'post',
									'fields' => array(),
									'buttons' => array(
										'reset' => array('active' => true, 'keyword' => 'Reset', 'type' => FormObjectTypes::RESET),
										'submit' => array('active' => true, 'keyword' => 'Submit', 'type' => FormObjectTypes::SUBMIT),
									)), $simpleForm = false) {

		if ( empty($model) ) {
			throw new FormBuilderException('Model Cannot Be Empty');
		}

		$this->simpleForm = $simpleForm;

		$this->HTML = null;
		$this->MODELS = array();
		$this->MODELS[] = $this->DEFAULT_MODEL = array(
			'model' => $model,
			'options' => $options
		);
	}

	/**
	 * Set Model
	 * @param $model
	 * @param array $options
	 * @return $this
	 * @throws FormBuilderException
	 */
	public function setModel($model, array $options = array(
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


		$this->MODELS[] = array(
			'model' => $model,
			'options' => $options
		);

		return $this;
	}

	/**
	 * Set Used Fields
	 * @param array $fields
	 * @return $this
	 */
	public function setUsedFields(array $fields) {
		$this->usedFields = $fields;

		return $this;
	}

	/**
	 * todo have to be implemented
	 * Unset Unused Fields
	 * @param array $fields
	 * @return $this
	 */
	public function unsetUnusedFields(array $fields) {

		$this->unusedFields = $fields;

		return $this;
	}

	/**
	 * todo have to be developed Form Validator
	 */
	public function validate() {
		$formValidator = new FormValidator($this->MODELS);

		return $formValidator->isValid();
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
	 * Build Forms
	 * @throws FormBuilderException
	 */
	private function buildForms() {
		$this->HTML = null;

		if ( empty($this->MODELS) ) {
			throw new FormBuilderException('Form model param cannot be empty');
		}

		$macros = array(
			FormObjects::MACROS_NAME,
			FormObjects::MACROS_TYPE,
			FormObjects::MACROS_VALUE,
			FormObjects::MACROS_PLACEHOLDER,
			FormObjects::MACROS_ATTRIBUTES,
			FormObjects::MACROS_OPTIONS);

		$objects = null;
		$i = 0;
		foreach($this->MODELS as $model) {
			$this->reader = new ModelFieldsReader($model['model']);


			if ($properties = $this->reader->getModelPropertiesTokens() ) {

				foreach($properties as $property) {
					$this->fieldValue = $this->reader->getValueByFieldName($property['field']);

					//skip fields which are not required or are primary key
					if (in_array($property['field'], $this->unusedFields)) {

						continue;
					}

					if ( !isset($property['type']) ) {
						throw new FormBuilderException('Missing property type');
					}

					$propertyFieldName = $this->reader->getModelTableName().'['.$i.']['.$property['field'].']';

					if ( $this->simpleForm ) {

						$propertyFieldName = $property['field'];
					}

					$options = $model['options'];

					//check exist property type inside of fields
					$propertyType = (isset($options['fields'][$property['field']]['type']) ?
											$options['fields'][$property['field']]['type'] : $property['type']);

					switch($propertyType) {
						case FormObjectTypes::HIDDEN: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects, FormObjectTypes::HIDDEN);
							break;
						}
						case ValueTypes::VALUE_TYPE_TINYINT:
						case ValueTypes::VALUE_TYPE_BIT:
						case ValueTypes::VALUE_TYPE_BOOLEAN:
						case ValueTypes::VALUE_TYPE_BOOL: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects, FormObjectTypes::CHECKBOX);

						break;
						}
						case ValueTypes::VALUE_TYPE_SMALLINT:
						case ValueTypes::VALUE_TYPE_MEDIUMINT:
						case ValueTypes::VALUE_TYPE_BIGINT:
						case ValueTypes::VALUE_TYPE_INT: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects, FormObjectTypes::NUMBER);

						break;
						}
						case ValueTypes::VALUE_TYPE_DECIMAL:
						case ValueTypes::VALUE_TYPE_DOUBLE:
						case ValueTypes::VALUE_TYPE_REAL:
						case ValueTypes::VALUE_TYPE_FLOAT: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects, FormObjectTypes::NUMBER);

						break;
						}
						case FormObjectTypes::SELECT:
						case ValueTypes::VALUE_TYPE_SET:
						case ValueTypes::VALUE_TYPE_ENUM: {
							$this->buildEnumSelectBox($model, $property, $propertyFieldName, $objects);

						break;
						}
						case FormObjectTypes::FILE:
						case ValueTypes::VALUE_TYPE_BLOB:
						case ValueTypes::VALUE_TYPE_LONGBLOB: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects, FormObjectTypes::FILE);

						break;
						}
						case ValueTypes::VALUE_TYPE_TIMESTAMP:
						case ValueTypes::VALUE_TYPE_DATETIME:
						case ValueTypes::VALUE_TYPE_DATE:
						case ValueTypes::VALUE_TYPE_TIME: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects, FormObjectTypes::DATETIME);

						break;
						}
						case ValueTypes::VALUE_TYPE_TEXT:
						case ValueTypes::VALUE_TYPE_MEDIUMTEXT:
						case ValueTypes::VALUE_TYPE_LONGTEXT: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects, FormObjectTypes::TEXTAREA);

						break;
						}
						case ValueTypes::VALUE_TYPE_STRING:
						case ValueTypes::VALUE_TYPE_VARCHAR:
						case ValueTypes::VALUE_TYPE_CHAR: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects);

						break;
						}
						default: {
							$this->buildInputTextObject($macros, $model, $propertyFieldName, $property, $objects);

						break;
						}
					}
				}

				if ( !$this->FORMS_UNIFY ) {

					$this->buildForm($this->reader, $model, $objects);
				}
			}
			$i++;
		}

		if ( $this->FORMS_UNIFY ) {

			$this->buildForm(new ModelFieldsReader($this->DEFAULT_MODEL['model']), $this->DEFAULT_MODEL, $objects);
		}
	}

	/**
	 * @param $macros
	 * @param $model
	 * @param $propertyFieldName
	 * @param $property
	 * @param $objects
	 * @param string $type
	 */
	private function buildInputTextObject($macros, $model, $propertyFieldName, $property, &$objects, $type = FormObjectTypes::TEXT) {
		$object = null;
		$classes = 'form-group';

		$objectClassName = 'form-control';

		if ( isset($property['field']) && isset($model['options']['fields'][$property['field']]['class']) ) {
			$objectClassName = $model['options']['fields'][$property['field']]['class'];
		}

		$attribute = null;
		if ( isset($property['field']) && isset($model['options']['fields'][$property['field']]['attribute']) ) {
			$attribute = $model['options']['fields'][$property['field']]['attribute'];
		}

		$fieldLabel = $property['field'];
		if ( isset($property['field']) && isset($model['options']['fields'][$property['field']]['label']) ) {
			$fieldLabel = $model['options']['fields'][$property['field']]['label'];
		}

		switch($type) {
			case FormObjectTypes::TEXT: {
				$object = str_replace(
						array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
						array(' for="'.$propertyFieldName.'"', FormTranslations::t('form', $fieldLabel)), FormObjects::LABEL) .
					str_replace($macros,
						array(
							$propertyFieldName,
							$type, $this->fieldValue, '', 'class="'.$objectClassName.'" id="'.$propertyFieldName.'" ' . $attribute),
						FormObjects::INPUT);

				break;
			}
			case FormObjectTypes::CHECKBOX: {
				$checked = '';
				$value = '';//$this->fieldValue;
				if ($this->fieldValue) {
					$checked = 'checked="checked"';
				}

				$object = str_replace(
						array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
						array(' for="'.$property['field'].'"', str_replace($macros,
							array(
								$propertyFieldName,
								$type, (string)$value, '', 'id="'.$propertyFieldName.'" ' . $checked . ' ' . $attribute),
							FormObjects::INPUT) . FormTranslations::t('form', $fieldLabel)), FormObjects::LABEL);
				$classes = 'checkbox';
				break;
			}
			case FormObjectTypes::TEXTAREA: {
				$object .= str_replace(
								array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
								array(' for="'.$propertyFieldName.'"', FormTranslations::t('form', $fieldLabel)), FormObjects::LABEL) .
							str_replace($macros, array($propertyFieldName,
								FormObjectTypes::TEXT, $this->fieldValue, '','class="'.$objectClassName.'"' . ' id="'.$propertyFieldName.'" ' . $attribute),
							FormObjects::TEXTAREA);

				break;
			}
			default: {
			$objectClassName = ($type === FormObjectTypes::FILE ? '' : $objectClassName);
			$label = str_replace(
				array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
				array(' for="'.$property['field'].'"', FormTranslations::t('form', $fieldLabel)), FormObjects::LABEL);

			if ( (isset($property['primary_key']) && $property['primary_key'] && isset($property['auto_increment']) && $property['auto_increment'] === 'true') || $type === FormObjectTypes::HIDDEN) {
				$type = FormObjectTypes::HIDDEN;
				$label = null;
			}

			$object =  $label.
				str_replace($macros,
					array(
						$propertyFieldName,
						$type, $this->fieldValue, '', 'class="'.$objectClassName.'" id="'.$property['field'].'"' . $attribute),
					FormObjects::INPUT);

				break;
			}
		}

		$objects .= str_replace(
			array(
				FormObjects::MACROS_ATTRIBUTES,
				FormObjects::MACROS_FIELDS),
			array('class="'.$classes.'"', $object),FormObjects::FIELDSET);

	}

	/**
	 * Build Enum Select Box
	 * @param $model
	 * @param $property
	 * @param $propertyFieldName
	 * @param $objects
	 */
	private function buildEnumSelectBox($model, $property, $propertyFieldName, &$objects) {
		$reader = new ModelFieldsReader($model['model']);
		$optionFields = $model['options'];
		$classes = 'form-group';

		$optionsResult = str_replace(
			array(FormObjects::MACROS_VALUE, FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
			array(FormTranslations::t('form', 'All'), '', FormTranslations::t('form', 'All')),
			FormObjects::SELECT_OPTION);

		$selectedValues = isset($optionFields['fields'][$property['field']]['selected_values']) ?
								$optionFields['fields'][$property['field']]['selected_values'] : array();

		$methodKey = (isset($optionFields['fields'][$property['field']]['key']) ? $optionFields['fields'][$property['field']]['key'] : $property['field']);
		$methodName = $this->getGetterMethod($methodKey);

		//get Data from Enum
		if ( !isset($optionFields['fields'][$property['field']]['type']) ) {

			$methodName .= 'List';
			$options = $reader->getModel()->{$methodName}();

			if ( $options ) {
				foreach($options AS $option) {
					$selected = '';
					if ( $this->fieldValue == $option || $this->checkValueInArray($methodKey, $option, $selectedValues)) {
						$selected = 'selected="selected"';
					}

					$optionsResult .= str_replace(
						array(FormObjects::MACROS_VALUE, FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
						array($option, $selected, ucfirst($option)),
						FormObjects::SELECT_OPTION);
				}
			}
		}
		else {//Get Data from Select
			$optionsData = isset($optionFields['fields'][$property['field']]['data']) ? $optionFields['fields'][$property['field']]['data'] : array();

			if ( $optionsData ) {
				foreach($optionsData AS $value => $optionData) {
					$optionDataKey = $optionData;
					$isObject = false;
					if ( is_object($optionData) ) {

						$value = $optionData->{$methodName}();
						if ( isset($optionFields['fields'][$property['field']]['fields']) && is_array($optionFields['fields'][$property['field']]['fields']) ) {
							$resultFieldsData = array();
							foreach($optionFields['fields'][$property['field']]['fields'] AS $fieldName ) {
								$fieldMethodName = $this->getGetterMethod($fieldName);

								$resultFieldsData[] = FormTranslations::t('form', $optionData->{$fieldMethodName}());
							}
							$optionDataKey = implode('|', $resultFieldsData);
						}
						$isObject = true;

						$selected = '';
						if ( $this->fieldValue == $value || $this->checkValueInArray($methodKey, $value, $selectedValues)) {
							$selected = 'selected="selected"';
						}
					}
					else {
						$selected = '';
						if ( $this->fieldValue == $value ) {
							$selected = 'selected="selected"';
						}
					}


					$optionsResult .= str_replace(
						array(FormObjects::MACROS_VALUE, FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
						array($value, $selected, ($isObject ? $optionDataKey : FormTranslations::t('form', $optionDataKey))),
						FormObjects::SELECT_OPTION);
				}
			}

		}

		$macros = array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_NAME, FormObjects::MACROS_OPTIONS);

		$fieldName = (isset($optionFields['fields'][$property['field']]['keyword']) ? $optionFields['fields'][$property['field']]['keyword'] : $property['field']);

		$objectClassName = 'form-control';

		if ( isset($property['field']) && isset($model['options']['fields'][$property['field']]['class']) ) {
			$objectClassName = $model['options']['fields'][$property['field']]['class'];
		}

		$attribute = null;
		if ( isset($property['field']) && isset($model['options']['fields'][$property['field']]['attribute']) ) {
			$attribute = $model['options']['fields'][$property['field']]['attribute'];
		}
		$multiple = null;
		if ( isset($property['field']) && isset($model['options']['fields'][$property['field']]['multiple']) ) {
			$multiple = ' multiple="" ';
			$propertyFieldName .= '[]';
		}

		$object = str_replace(
			array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
			array(' for="'.$propertyFieldName.'"', FormTranslations::t('form', $fieldName)), FormObjects::LABEL).
			str_replace($macros,array('class="'.$objectClassName.'"' . ' id="'.$propertyFieldName.'"' . ' ' . $attribute . $multiple, $propertyFieldName, $optionsResult, ''),
			FormObjects::SELECT);


		$objects .= str_replace(
			array(
				FormObjects::MACROS_ATTRIBUTES,
				FormObjects::MACROS_FIELDS),
			array('class="'.$classes.'"', $object),FormObjects::FIELDSET);
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
				$methodName = $this->getGetterMethod($fieldName);
				foreach($selectedValues as $selectedObject) {
					if ( $value === $selectedObject->{$methodName}()) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Get Getter method
	 * @param $fieldName
	 * @return string
	 */
	private function getGetterMethod($fieldName) {
		$methodName = 'get'.implode('',array_map(function($field){
				return ucfirst($field);
			}, explode('_', $fieldName)));

		return $methodName;
	}

	/**
	 * Build an Form
	 * @param ModelFieldsReader $reader
	 * @param $model
	 * @param $objects
	 */
	public function buildForm(ModelFieldsReader $reader, $model, &$objects) {

		$action = (isset($model['options']['action']) ? $model['options']['action'] : null);
		$method = (isset($model['options']['method']) ? $model['options']['method'] : FormObjects::FORM_METHOD_POST);

		$form = str_replace(
			array(
				FormObjects::MACROS_ACTION,
				FormObjects::MACROS_METHOD,
				FormObjects::MACROS_NAME,
				FormObjects::MACROS_ATTRIBUTES),
			array($action,
				$method,
				$reader->getModelTableName(),
				str_replace(FormObjects::MACROS_ID,
					$reader->getModelTableName(),
					FormObjects::ID_NAME)),
			FormObjects::FORM);

		$buttonObjects = null;

		if (isset($model['options']['buttons'])) {

			foreach($model['options']['buttons'] as $key => $button) {

				if ( $key === 'submit' ) {

					$buttonObjects .= str_replace(
						array(
							FormObjects::MACROS_NAME,
							FormObjects::MACROS_TYPE,
							FormObjects::MACROS_PLACEHOLDER,
							FormObjects::MACROS_TEXT,
							FormObjects::MACROS_ATTRIBUTES),
						array(
							$reader->getModelTableName().'_submit',
							FormObjectTypes::SUBMIT, '', FormTranslations::t('form', $button['keyword']), 'class="btn btn-primary"'),
						FormObjects::BUTTON);
				}
				elseif ( $key === 'reset' && isset($button['active']) && $button['active']) {

					$buttonObjects .= str_replace(
						array(
							FormObjects::MACROS_NAME,
							FormObjects::MACROS_TYPE,
							FormObjects::MACROS_PLACEHOLDER,
							FormObjects::MACROS_TEXT,
							FormObjects::MACROS_ATTRIBUTES),
						array(
							$reader->getModelTableName().'_reset',
							FormObjectTypes::RESET, '', FormTranslations::t('form', $button['keyword']), 'class="btn btn-default"'),
						FormObjects::BUTTON);
				}
				else {

					$buttonType = (isset($button['type']) ? $button['type'] : FormObjectTypes::BUTTON);
					$className = (isset($button['class']) ? $button['class'] : '');

					$buttonObjects .= str_replace(
						array(
							FormObjects::MACROS_NAME,
							FormObjects::MACROS_TYPE,
							FormObjects::MACROS_PLACEHOLDER,
							FormObjects::MACROS_TEXT,
							FormObjects::MACROS_ATTRIBUTES),
						array(
							$reader->getModelTableName(),
							$buttonType, '', FormTranslations::t('form', $button['keyword']), 'class="btn btn-primary '.$className.'"'),
						FormObjects::BUTTON);
				}
			}

		}

		$sectionClass = (isset($model['options']['section_class']) ? $model['options']['section_class'] : 'col-md-6');

		$this->HTML .= str_replace(array(FormObjects::MACROS_FIELDS, FormObjects::MACROS_SECTION_CLASS), array($objects . $buttonObjects, $sectionClass), $form);
		$objects = null;
	}

	public function getHTML() {
		return $this->HTML;
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