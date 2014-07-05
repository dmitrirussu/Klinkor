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
	private $options = array();
	private $usedFields = array();
	private $unusedFields = array();

	private static $FORMS_UNIFY = false;
	private static $MODELS = array();
	private static $HTML;

	/**
	 * @var \OmlManager\ORM\Models\Reader
	 */
	private static $DEFAULT_MODEL;

	/**
	 * @param null $model
	 * @param array $options
	 * @throws FormBuilderException
	 */
	public function __construct($model = null,
								array $options = array(
									'action' => '',
									'method' => 'post',
									'fields' => array(),
									'buttons' => array(
										'submit' => 'Submit',
										'reset' => array('active' => true, 'keyword' => 'Reset')))) {

		if ( empty($model) ) {
			throw new FormBuilderException('Model Cannot Be Empty');
		}

		self::$MODELS[] = self::$DEFAULT_MODEL = array(
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
								'action' => '',
								'method' => 'post',
								'fields' => array(),
								'buttons' => array(
									'submit' => 'Submit',
									'reset' => array('active' => true, 'keyword' => 'Reset')))) {

		if ( empty($model) ) {

			throw new FormBuilderException('Form model param cannot be empty');
		}

		self::$MODELS[] = array(
			'model' => $model,
			'options' => $options
		);

		return $this;
	}

	/**
	 * Set Used Fields
	 * @param array $fields
	 * @param array $options
	 * @return $this
	 */
	public function setUsedFields(array $fields, $options = array()) {
		$this->usedFields = $fields;
		$this->options = $options;

		return $this;
	}

	/**
	 * todo have to be implemented
	 * Unset Unused Fields
	 * @param array $fields
	 * @return $this
	 */
	public function unsetUnusedFields(array $fields) {

		return $this;
	}

	/**
	 * todo have to be developed Form Validator
	 */
	public function validate() {
		$formValidator = new FormValidator(self::$MODELS);

		return $formValidator->isValid();
	}

	public function getForm() {
		self::buildForms();
		return self::$HTML;
	}

	public static function show() {
		self::buildForms();
		return print(self::$HTML);
	}

	/**
	 * Build Forms
	 * @throws FormBuilderException
	 */
	private static function buildForms() {
		self::$HTML = null;

		if ( empty(self::$MODELS) ) {
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
		foreach(self::$MODELS as $model) {
			$reader = new ModelFieldsReader($model['model']);
			if ($properties = $reader->getModelPropertiesTokens() ) {

				foreach($properties as $property) {

					if ( isset($property['primary_key']) && $property['primary_key'] && isset($property['auto_increment']) && $property['auto_increment'] === 'true') {
						continue;
					}

					if ( !isset($property['type']) ) {
						throw new FormBuilderException('Missing property type');
					}

					$propertyFieldName = $property['field'];
					if ( self::$FORMS_UNIFY ) {
						$propertyFieldName = '['.$reader->getModelTableName().']['.$property['field'].']';
					}

					$options = $model['options'];

					//check exist property type inside of fields
					$propertyType = (isset($options['fields'][$propertyFieldName]['type']) ?
											$options['fields'][$propertyFieldName]['type'] : $property['type']);

					switch($propertyType) {
						case ValueTypes::VALUE_TYPE_TINYINT:
						case ValueTypes::VALUE_TYPE_BIT:
						case ValueTypes::VALUE_TYPE_BOOLEAN:
						case ValueTypes::VALUE_TYPE_BOOL: {
							self::buildInputTextObject($macros, $propertyFieldName, $objects, FormObjectTypes::CHECKBOX);

						break;
						}
						case ValueTypes::VALUE_TYPE_SMALLINT:
						case ValueTypes::VALUE_TYPE_MEDIUMINT:
						case ValueTypes::VALUE_TYPE_BIGINT:
						case ValueTypes::VALUE_TYPE_INT: {
							self::buildInputTextObject($macros, $propertyFieldName, $objects, FormObjectTypes::NUMBER);

						break;
						}
						case ValueTypes::VALUE_TYPE_DECIMAL:
						case ValueTypes::VALUE_TYPE_DOUBLE:
						case ValueTypes::VALUE_TYPE_REAL:
						case ValueTypes::VALUE_TYPE_FLOAT: {
							self::buildInputTextObject($macros, $propertyFieldName, $objects, FormObjectTypes::NUMBER);

						break;
						}
						case FormObjectTypes::SELECT:
						case ValueTypes::VALUE_TYPE_SET:
						case ValueTypes::VALUE_TYPE_ENUM: {
							self::buildEnumSelectBox($model, $property, $propertyFieldName, $objects);

						break;
						}
						case ValueTypes::VALUE_TYPE_BLOB:
						case ValueTypes::VALUE_TYPE_LONGBLOB: {
							self::buildInputTextObject($macros, $propertyFieldName, $objects, FormObjectTypes::FILE);

						break;
						}
						case ValueTypes::VALUE_TYPE_TIMESTAMP:
						case ValueTypes::VALUE_TYPE_DATETIME:
						case ValueTypes::VALUE_TYPE_DATE:
						case ValueTypes::VALUE_TYPE_TIME: {
							self::buildInputTextObject($macros, $propertyFieldName, $objects, FormObjectTypes::DATETIME);

						break;
						}
						case ValueTypes::VALUE_TYPE_TEXT:
						case ValueTypes::VALUE_TYPE_MEDIUMTEXT:
						case ValueTypes::VALUE_TYPE_LONGTEXT: {
							self::buildInputTextObject($macros, $propertyFieldName, $objects, FormObjectTypes::TEXTAREA);

						break;
						}
						case ValueTypes::VALUE_TYPE_STRING:
						case ValueTypes::VALUE_TYPE_VARCHAR:
						case ValueTypes::VALUE_TYPE_CHAR: {
							self::buildInputTextObject($macros, $propertyFieldName, $objects);

						break;
						}
						default: {
							self::buildInputTextObject($macros, $propertyFieldName, $objects);

						break;
						}
					}
				}

				if ( !self::$FORMS_UNIFY ) {

					self::buildForm($reader, $model, $objects);
				}
			}
		}

		if ( self::$FORMS_UNIFY ) {

			self::buildForm(new ModelFieldsReader(self::$DEFAULT_MODEL['model']), self::$DEFAULT_MODEL, $objects);
		}
	}

	/**
	 * @param $macros
	 * @param $propertyFieldName
	 * @param $objects
	 * @param string $type
	 */
	private static function buildInputTextObject($macros, $propertyFieldName, &$objects, $type = FormObjectTypes::TEXT) {
		$object = null;
		$classes = 'form-group';

		switch($type) {
			case FormObjectTypes::TEXT: {
				$object = str_replace(
						array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
						array(' for="'.$propertyFieldName.'"', FormTranslations::t('form', $propertyFieldName)), FormObjects::LABEL) .
					str_replace($macros,
						array(
							$propertyFieldName,
							$type, '', '', 'class="form-control" id="'.$propertyFieldName.'"'),
						FormObjects::INPUT);

				break;
			}
			case FormObjectTypes::CHECKBOX: {
				$object = str_replace(
						array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
						array(' for="'.$propertyFieldName.'"', str_replace($macros,
							array(
								$propertyFieldName,
								$type, '', '', 'id="'.$propertyFieldName.'"'),
							FormObjects::INPUT) . FormTranslations::t('form', $propertyFieldName)), FormObjects::LABEL);
				$classes = 'checkbox';
				break;
			}
			case FormObjectTypes::TEXTAREA: {
				$objects .= str_replace($macros, array($propertyFieldName,
						FormObjectTypes::TEXT, '','','class="form-control"'),
					FormObjects::TEXTAREA);

				break;
			}
			default: {

			$object = str_replace(
					array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
					array(' for="'.$propertyFieldName.'"', FormTranslations::t('form', $propertyFieldName)), FormObjects::LABEL) .
				str_replace($macros,
					array(
						$propertyFieldName,
						$type, '', '', 'class="form-control" id="'.$propertyFieldName.'"'),
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
	private static function buildEnumSelectBox($model, $property, $propertyFieldName, &$objects) {
		$reader = new ModelFieldsReader($model['model']);
		$optionFields = $model['options'];

		$optionsResult = str_replace(
			array(FormObjects::MACROS_VALUE, FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
			array(FormTranslations::t('form', 'All'), '', FormTranslations::t('form', 'All')),
			FormObjects::SELECT_OPTION);

		if ( !isset($optionFields['fields'][$property['field']]['type']) ) {
			//get Data from Enum
			$methodName = 'get'.implode('',array_map(function($field){
					return ucfirst($field);
				}, explode('_', $property['field']))).'List';

			$options = $reader->getModel()->{$methodName}();

			if ( $options ) {
				foreach($options AS $option) {
					$optionsResult .= str_replace(
						array(FormObjects::MACROS_VALUE, FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
						array($option, '', ucfirst($option)),
						FormObjects::SELECT_OPTION);
				}
			}
		}
		else  {
			$methodName = self::getGetterMethod($property['field']);

			$optionsData = $optionFields['fields'][$property['field']]['data'];

			foreach($optionsData AS $value => $optionData) {
				$optionDataKey = $optionData;
				$isObject = false;
				if ( is_object($optionData) ) {

					$value = $optionData->{$methodName}();
					if ( isset($optionFields['fields'][$property['field']]['fields']) && is_array($optionFields['fields'][$property['field']]['fields']) ) {
						$resultFieldsData = array();
						foreach($optionFields['fields'][$property['field']]['fields'] AS $fieldName ) {
							$fieldMethodName = self::getGetterMethod($fieldName);

							$resultFieldsData[] = FormTranslations::t('form', $optionData->{$fieldMethodName}());
						}
						$optionDataKey = implode('|', $resultFieldsData);
					}
					$isObject = true;
				}

				$optionsResult .= str_replace(
					array(FormObjects::MACROS_VALUE, FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_TEXT),
					array($value, '', ($isObject ? $optionDataKey : FormTranslations::t('form', $optionDataKey))),
					FormObjects::SELECT_OPTION);
			}
		}

		$macros = array(FormObjects::MACROS_ATTRIBUTES, FormObjects::MACROS_NAME, FormObjects::MACROS_OPTIONS);

		$objects .= str_replace($macros,array('class="form-control"', $propertyFieldName, $optionsResult, ''),
			FormObjects::SELECT);
	}

	/**
	 * Get Getter method
	 * @param $fieldName
	 * @return string
	 */
	private static function getGetterMethod($fieldName) {
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
	private static function buildForm(ModelFieldsReader $reader, $model, &$objects) {
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

		$buttonSubmit = str_replace(
			array(
				FormObjects::MACROS_NAME,
				FormObjects::MACROS_TYPE,
				FormObjects::MACROS_PLACEHOLDER,
				FormObjects::MACROS_TEXT,
				FormObjects::MACROS_ATTRIBUTES),
			array(
				$reader->getModelTableName().'_submit',
				FormObjectTypes::SUBMIT, '', FormTranslations::t('form', $model['options']['buttons']['submit']), 'class="btn btn-primary"'),
			FormObjects::BUTTON);
		$buttonReset = null;
		if ( isset($model['options']['buttons']['reset']['active']) && $model['options']['buttons']['reset']['active']) {

			$buttonReset = str_replace(
				array(
					FormObjects::MACROS_NAME,
					FormObjects::MACROS_TYPE,
					FormObjects::MACROS_PLACEHOLDER,
					FormObjects::MACROS_TEXT,
					FormObjects::MACROS_ATTRIBUTES),
				array(
					$reader->getModelTableName().'_reset',
					FormObjectTypes::RESET, '', FormTranslations::t('form', $model['options']['buttons']['reset']['keyword']), 'class="btn btn-default"'),
				FormObjects::BUTTON);
		}

		self::$HTML .= str_replace(FormObjects::MACROS_FIELDS, $objects . $buttonReset . $buttonSubmit, $form);
		$objects = null;
	}

	/**
	 * Forms Unify
	 * @return $this
	 */
	public function unify() {
		self::$FORMS_UNIFY = true;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getErrors() {

		return $this->errors;
	}
} 